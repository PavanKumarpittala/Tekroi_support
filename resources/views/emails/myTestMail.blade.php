@isset($title)
<p>{{$title ?? null}}</p>
@endisset

@isset($body)
{!! $body ?? null !!}
@endisset

@isset($ticket_id)
    <!-- <h4>Ticket Details</h4> -->
    <p><b>Ticket Id</b> : {{$ticket_id }}</p>
    <p><b>Ticket Message</b> : </p>
    <p>{!! $ticket_message !!}</p>
@endisset

