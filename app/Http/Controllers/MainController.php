<?php

namespace App\Http\Controllers;

use File;
use Illuminate\Http\Request;

class MainController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('public.main');
    }

    public function analyse()
    {
        $json = json_decode($this->getJSON('example.json'));
        $members = $json->members;
        $lists = $json->lists;
        $actions = $this->parseActions(array_reverse($json->actions));
        return view('public.analyse', ['members' => $members, 'lists' => $lists, 'actions' => $actions]);
    }

    private function getJSON($filename)
    {
        $path = storage_path() . '/app/public/json/' . $filename;
        if (! File::exists($path)) {
            die('Invalid file: ' . $path);
        }

        return File::get($path);
    }

    public function getTimelineJSON()
    {
        $json = json_decode($this->getJSON('example.json'));
        echo $this->convertJSONtoTimelineFormat($json);
    }

    private function convertJSONtoTimelineFormat($json)
    {
        $return = [
            'title' => [
                'headline' => 'Testing Timeline',
                'text' => 'Just testing innit'
                ],
            'events' => []
            ];
        $parsed_json = $this->parseActions(array_reverse($json->actions));
        $months = $parsed_json['months'];
        foreach ($months as $month) {
            // echo '<pre>'; print_r($month['timeline']); echo '</pre>';
            foreach ($month['timeline'] as $action_type => $actions) {
                foreach ($actions as $action) {
                    // Trello format: 2016-07-06T14:43:30.176Z
                    // echo '<p><strong>'.$action_type.'</strong></p>';
                    // echo '<pre>'; print_r($action); echo '</pre>';
                    if ($action_type != 'other') {
                        if ($action_type == 'movedCards') {
                            $action_type = 'moved a card.';
                            $text = '<p>' . $action['member']['name'] . ' moved this card:</p>';
                            $text .= '<p>' . $action['card']['name'] . '</p>';
                            $text .= '<p>from <strong>' . $action['oldList'] . '</strong>';
                            $text .= ' to <strong>' . $action['newList'] . '</strong></p>';
                        } elseif ($action_type == 'createdCards') {
                            $action_type = 'created a card.';
                            $text = '<p>' . $action['member']['name'] . ' created this card:</p>';
                            $text .= '<p>' . $action['card']['name'] . '</p>';
                        }
                        list($date, $time) = explode('T', $action['date']);
                        list($year, $month, $day) = explode('-', $date);
                        list($hour, $minute, $second) = explode(':', $time);
                        list($second, $millisecond) = explode('.', $second);
                        $return['events'][] = [
                            'start_date' => [
                                'year' => $year,
                                'month' => $month,
                                'day' => $day,
                                'hour' => $hour,
                                'minute' => $minute,
                                'second' => $second
                            ],
                            'text' => [
                                'headline' => $action['member']['initials'] . ' ' . $action_type,
                                'text' => $text
                            ]
                        ];
                    }
                }
            }
        }
        return json_encode($return);
    }

    private function parseActions($actions)
    {
        $earliestDate = strtotime($this->earliestDate($actions));
        $latestDate = strtotime($this->latestDate($actions));
        $return = [
            'earliestDate' => $earliestDate,
            'latestDate'   => $latestDate,
            'months'       => []
            ];
        $currentMonth = date('m', $earliestDate);
        $currentYear = date('Y', $earliestDate);
        $lastMonth = date('m', $latestDate);
        $lastYear = date('Y', $latestDate);
        while ($currentMonth <= $lastMonth && $currentYear <= $lastYear) {
            $theseActions = $this->getActionsByMonth($actions, $currentMonth, $currentYear);
            $return['months'][] = [
                'month'    => $currentMonth,
                'year'     => $currentYear,
                'actions'  => $theseActions,
                'timeline' => $this->createTimeline($theseActions)
                ];
            $currentMonth++;
            if ($currentMonth > 12) $currentYear++;
        }

        return $return;
    }

    private function createTimeline($actions)
    {
        $result = ['createdCards' => [], 'deletedCards' => [], 'movedCards' => [], 'other' => []];
        foreach ($actions['createCard'] as $action) {
            $result['createdCards'][] = [
                'date' => $action->date,
                'member' => [
                    'name' => $action->memberCreator->fullName,
                    'initials' => $action->memberCreator->initials
                    ],
                'card' => [
                    'name' => $action->data->card->name
                ]
            ];
        }
        foreach ($actions['deleteCard'] as $action) {
            $result['deletedCards'][] = [
                'date' => $action->date,
                'member' => [
                    'name' => $action->memberCreator->fullName,
                    'initials' => $action->memberCreator->initials
                    ],
                'card' => [
                    'id' => $action->data->card->id
                ]
            ];
        }
        foreach ($actions['moveCard'] as $action) {
            $result['movedCards'][] = [
                'date' => $action->date,
                'member' => [
                    'name' => $action->memberCreator->fullName,
                    'initials' => $action->memberCreator->initials
                    ],
                'card' => [
                    'name' => $action->data->card->name
                ],
                'oldList' => $action->data->listBefore->name,
                'newList' => $action->data->listAfter->name
            ];
        }
        foreach ($actions['otherUpdate'] as $action) {
            $result['other'][] = $action;
        }
        return $result;
    }

    private function isActionRelevant($action)
    {
        $ignoreActions = ['addMemberToCard', 'removeMemberFromCard', 'commentCard', 'addAttachmentToCard', 'deleteAttachmentFromCard', 'updateCheckItemStateOnCard', 'addChecklistToCard'];
        if (! in_array($action->type, $ignoreActions)) {
            return true;
        }
        return false;
    }

    private function getActionsByMonth($actions, $month, $year)
    {
        $return = ['createCard' => [], 'deleteCard' => [], 'moveCard' => [], 'otherUpdate' => []];
        foreach ($actions as $action) {
            if ($this->isActionRelevant($action)) {
                $date = strtotime($action->date);
                if (date('m', $date) == $month && date('Y', $date) == $year) {
                    if ($action->type == 'createCard') {
                        $return['createCard'][] = $action;
                    } else if ($action->type == 'deleteCard') {
                        $return['deleteCard'][] = $action;
                    } else if ($action->type == 'updateCard') {
                        if (isset($action->data->listBefore)) {
                            $return['moveCard'][] = $action;
                        } else {
                            $return['otherUpdate'][] = $action;
                        }
                    }
                }
            }
        }
        return $return;
    }

    private function earliestDate($actions)
    {
        return $actions[0]->date;
    }

    private function latestDate($actions)
    {
        return $actions[sizeof($actions) - 1]->date;
    }

    private function parseTrelloDate($date)
    {

    }
}
