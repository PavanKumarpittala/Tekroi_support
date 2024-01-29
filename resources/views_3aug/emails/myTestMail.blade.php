@isset($title)
<p>{{$title ?? null}}</p>
@endisset

@isset($body)
<p>{{$body ?? null}}</p>
@endisset

@isset($ticket_id)
    <h4>Ticket Details</h4>
    <p>Ticket Id : {{$ticket_id }}</p>
    <p>Ticket Message : </p>
    <p><pre>{{$ticket_message }} <pre> </p>
@endisset

