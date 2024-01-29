@isset($title)
<p>{{$title ?? null}}</p>
@endisset

@isset($body)
<p>{{$body ?? null}}</p>
@endisset

@isset($ticket_id)
    <!-- <h4>Ticket Details</h4> -->
    <p><b>Ticket Id</b> : {{$ticket_id }}</p>
    <p><b>Ticket Message</b> : </p>
    <p><pre>{{$ticket_message }} <pre> </p>
@endisset

