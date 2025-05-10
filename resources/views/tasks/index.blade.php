<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <title>Laravel Task Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-100 transition-colors duration-300 p-4">

    <div class="max-w-7xl mx-auto bg-white dark:bg-gray-800 rounded shadow p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl md:text-3xl font-bold">Task Manager</h1>

            <div class="flex items-center gap-4">
                <!-- Dark Mode Toggle -->
                <button onclick="document.documentElement.classList.toggle('dark')" class="px-4 py-2 bg-gray-300 dark:bg-gray-700 rounded">
                    Toggle Dark Mode
                </button>

                <!-- Auth Dropdown -->
                <!-- Auth Dropdown -->
                @auth
                <div class="relative inline-block text-left" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                    <button class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded focus:outline-none text-sm text-gray-800 dark:text-white">
                        {{ Auth::user()->name }}
                    </button>
                    <div x-show="open"
                        x-transition
                        class="absolute right-0 mt-2 bg-white dark:bg-gray-700 rounded shadow-md min-w-max z-50"
                        @mouseenter="open = true" @mouseleave="open = false">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-red-600 hover:bg-gray-100 dark:hover:bg-gray-800">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
                @endauth

                @guest
                <div class="relative inline-block text-left" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                    <button class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded focus:outline-none text-sm text-gray-800 dark:text-white">
                        Profile
                    </button>
                    <div x-show="open"
                        x-transition
                        class="absolute right-0 mt-2 bg-white dark:bg-gray-700 rounded shadow-md min-w-max z-50"
                        @mouseenter="open = true" @mouseleave="open = false">
                        <a href="{{ route('login') }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-800 text-sm text-gray-800 dark:text-white">
                            Sign In
                        </a>
                        <a href="{{ route('register') }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-800 text-sm text-gray-800 dark:text-white">
                            Register
                        </a>
                    </div>
                </div>
                @endguest
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <button class="md:hidden bg-blue-600 text-white px-3 py-2 rounded mb-3" onclick="document.getElementById('taskForm').classList.toggle('hidden')">
                    Toggle Task Form
                </button>

                <div id="taskForm" class="space-y-4 md:block {{ isset($task) ? '' : 'hidden md:block' }}">
                    <form method="GET" action="{{ route('tasks.index') }}" class="flex gap-2">
                        <input type="text" name="search" placeholder="Search tasks..." value="{{ request('search') }}"
                            class="flex-grow border p-2 rounded dark:bg-gray-700 dark:border-gray-600" />
                        <button type="submit"
                            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Search</button>
                    </form>

                    <form method="POST" action="{{ isset($task) ? route('tasks.update', $task) : route('tasks.store') }}" class="space-y-3">
                        @csrf
                        @if(isset($task)) @method('PUT')
                        @endif
                        <input type="text" name="title" placeholder="Title" required
                            value="{{ old('title', $task->title ?? '') }}"
                            class="w-full border p-2 rounded dark:bg-gray-700 dark:border-gray-600" />

                        <textarea name="description" placeholder="Description" required
                            class="w-full border p-2 rounded dark:bg-gray-700 dark:border-gray-600">{{ old('description', $task->description ?? '') }}</textarea>

                        <select name="priority"
                            class="w-full border p-2 rounded dark:bg-gray-700 dark:border-gray-600">
                            @foreach(['Low', 'Medium', 'High'] as $level)
                                <option value="{{ $level }}" {{ old('priority', $task->priority ?? 'Medium') === $level ? 'selected' : '' }}>{{ $level }}</option>
                            @endforeach
                        </select>

                        <input type="date" name="due_date"
                            value="{{ old('due_date', $task->due_date ?? '') }}"
                            class="w-full border p-2 rounded dark:bg-gray-700 dark:border-gray-600" />

                        <input type="text" name="tags" placeholder="Tags (e.g. Work,Urgent)"
                            value="{{ old('tags', isset($task) ? $task->tags->pluck('name')->implode(',') : '') }}"
                            class="w-full border p-2 rounded dark:bg-gray-700 dark:border-gray-600" />

                        <label class="inline-flex items-center">
                            <input type="checkbox" name="is_completed" value="1" class="mr-2"
                                {{ old('is_completed', $task->is_completed ?? false) ? 'checked' : '' }}>
                            <span>Mark as Completed</span>
                        </label>

                        <button type="submit"
                            class="w-full bg-{{ isset($task) ? 'green' : 'blue' }}-600 text-white py-2 rounded hover:bg-{{ isset($task) ? 'green' : 'blue' }}-700 transition">
                            {{ isset($task) ? 'Update Task' : 'Add Task' }}
                        </button>
                    </form>
                </div>
            </div>

            <div>
                <form method="GET" action="{{ route('tasks.index') }}" class="mb-4 flex flex-wrap gap-2">
                    <select name="priority" class="border p-2 rounded dark:bg-gray-700 dark:border-gray-600">
                        <option value="">All Priorities</option>
                        @foreach(['High', 'Medium', 'Low'] as $level)
                            <option value="{{ $level }}" {{ request('priority') == $level ? 'selected' : '' }}>{{ $level }}</option>
                        @endforeach
                    </select>

                    <select name="tag" class="border p-2 rounded dark:bg-gray-700 dark:border-gray-600">
                        <option value="">All Tags</option>
                        @foreach ($allTags as $tag)
                            <option value="{{ $tag }}" {{ request('tag') == $tag ? 'selected' : '' }}>{{ $tag }}</option>
                        @endforeach
                    </select>

                    <select name="sort" class="border p-2 rounded dark:bg-gray-700 dark:border-gray-600">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Newest First</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                    </select>

                    <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Apply</button>
                </form>

                <div class="mb-4 space-x-2">
                    @php $status = request('status'); @endphp
                    <a href="{{ route('tasks.index') }}"
                        class="px-3 py-1 rounded {{ $status === null ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 dark:text-white' }}">
                        All
                    </a>
                    <a href="{{ route('tasks.index', ['status' => 'completed']) }}"
                        class="px-3 py-1 rounded {{ $status === 'completed' ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 dark:text-white' }}">
                        Completed
                    </a>
                    <a href="{{ route('tasks.index', ['status' => 'incomplete']) }}"
                        class="px-3 py-1 rounded {{ $status === 'incomplete' ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 dark:text-white' }}">
                        Incomplete
                    </a>
                </div>

                @if ($tasks->count())
                <div class="overflow-y-auto scroll-smooth" style="max-height: 70vh;">
                    <ul class="space-y-3 pr-2">
                        @foreach ($tasks as $task)
                            <li class="border-b p-3 rounded dark:border-gray-600 {{ $task->due_date && \Carbon\Carbon::parse($task->due_date)->isPast() && !$task->is_completed ? 'bg-red-100 dark:bg-red-700' : '' }}">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-lg font-semibold">{{ $task->title }}</h3>
                                        <p class="text-sm">{{ $task->description }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-300">
                                            Priority: {{ $task->priority }} |
                                            Due: {{ $task->due_date ?? 'No due date' }}
                                        </p>
                                    </div>

                                    <div class="text-right text-sm space-y-1">
                                        <a href="{{ route('tasks.edit', $task) }}" class="text-blue-600 hover:underline">Edit</a>
                                        <form method="POST" action="{{ route('tasks.destroy', $task) }}" onsubmit="return confirm('Delete this task?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                        </form>
                                        @if (!$task->is_completed)
                                            <form method="POST" action="{{ route('tasks.toggle', $task->id) }}">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="text-green-600 hover:underline">Mark Done</button>
                                            </form>
                                        @else
                                            <span class="text-green-500">✔ Completed</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="mt-2 flex flex-wrap gap-1">
                                    @foreach ($task->tags as $tag)
                                        <span class="text-xs bg-gray-300 dark:bg-gray-700 px-2 py-1 rounded-full">{{ $tag->name }}</span>
                                    @endforeach
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
                @else
                    <p class="text-gray-500 dark:text-gray-300">No tasks found.</p>
                @endif
            </div>
        </div>
    </div>

</body>
</html>
