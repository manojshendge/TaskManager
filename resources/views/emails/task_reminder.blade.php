<h2>Hello {{ $user->name }},</h2>
<p>You have the following tasks due in 3 days:</p>
<ul>
@foreach ($tasks as $task)
    <li>{{ $task->title }} - Due: {{ $task->due_date->format('Y-m-d') }}</li>
@endforeach
</ul>
<p>Please make sure to complete them on time!</p>
