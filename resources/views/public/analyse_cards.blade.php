@extends('public.layouts.layout')

@section('content')
<section class="section">

    <p class="title">Analysis</p>


    <div id='timeline-embed' style="width: 100%; height: 600px"></div>


    <div class="tabs is-boxed">
        <ul>
            <li><a href="#">Members</a></li>
            <li><a href="#">Columns</a></li>
            <li><a href="#">Actions</a></li>
        </ul>
    </div>

    <div id="members" style="display:none">
        <ul>
            @foreach ($members as $member)
                <li>{{ $member->fullName }}</li>
            @endforeach
        </ul>
    </div>

    <div id="columns" style="display:none">
        <ul>
            @foreach ($lists as $list)
                <li>{{ $list->name }}</li>
            @endforeach
        </ul>
    </div>

    <div id="actions">
        <p>Earliest date: {{ date('Y-m-d H:m:s', $actions['earliestDate']) }}; Latest date: {{ date('Y-m-d H:m:s', $actions['latestDate']) }}</p>
        @foreach ($actions['months'] as $actionSet)
            <?php $c = 0 ?>
            <div class="message">
                <div class="message-header">
                    {{ $actionSet['month'] . '/' . $actionSet['year'] }}
                </div>
                <div class="message-body">
                    <div class="tabs is-boxed cardTypeTabs">
                        <ul>
                            <li><a href="#" class="createdCue" id="cue<?=$c?>">Created Cards</a></li>
                            <li><a href="#" class="movedCue" id="cue<?=$c?>">Moved Cards</a></li>
                        </ul>
                    </div>
                    <div class="createdCards" id="createdContent<?=$c?>" style="-webkit-flex-direction: row;flex-direction: row;">
                        <p class="bg-info"><strong>Created Cards:</strong></p>
                        @foreach ($actionSet['timeline']['createdCards'] as $action)
                            <div class="card">
                                <header class="card-header">
                                    <strong>Date:</strong> {{ $action['date'] }}<br>
                                </header>
                                <div class="card-content">
                                    <strong>Member:</strong> {{ $action['member'] }}<br>
                                    <strong>Card created:</strong> {{ $action['card']['name'] }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="movedCards" style="display:none" id="movedContent<?=$c?>" style="-webkit-flex-direction: row;flex-direction: row;">
                        <p class="bg-info"><strong>Moved Cards</strong></p>
                        @foreach ($actionSet['timeline']['movedCards'] as $action)
                            <div class="card">
                                <header class="card-header">
                                    <strong>Date:</strong> {{ $action['date'] }}<br>
                                </header>
                                <div class="card-content">
                                    <strong>Member:</strong> {{ $action['member'] }}<br>
                                    <strong>Card moved:</strong> {{ $action['card']['name'] }}<br>
                                    <strong>From</strong> {{ $action['oldList'] }} <strong>to</strong> {{ $action['newList'] }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <?php $c++; ?>
        @endforeach
        <?php
            echo '<pre>'; print_r($actions['months']); echo '</pre>';
        ?>
    </div>

</section>
@endsection
