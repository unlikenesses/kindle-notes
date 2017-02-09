@extends('public.layouts.layout')

@section('content')
<section class="section">

    <p class="title">Analysis</p>


    <div id='timeline-embed' style="width: 100%; height: 600px"></div>



        <?php
            echo '<pre>'; print_r($actions['months']); echo '</pre>';
        ?>
    </div>

</section>
@endsection
