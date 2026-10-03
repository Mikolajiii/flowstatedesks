<x-admin-layout>
    <h1 class="text-2xl font-bold mb-4">Webhook Events</h1>

    <table class="w-full text-left border">
        <thead class="bg-gray-100">
        <tr>
            <th class="p-2">ID</th>
            <th class="p-2">Event Type</th>
            <th class="p-2">Received</th>
            <th class="p-2">Actions</th>
        </tr>
        </thead>

        <tbody>
        @foreach ($webhooks as $hook)
            <tr class="border-t">
                <td class="p-2">{{ $hook->id }}</td>
                <td class="p-2">{{ $hook->payload['type'] ?? 'unknown' }}</td>
                <td class="p-2">{{ $hook->created_at }}</td>
                <td class="p-2">
                    <button onclick="toggleJson({{ $hook->id }})"
                            class="text-blue-600 hover:underline">
                        View JSON
                    </button>
                </td>
            </tr>

            <tr id="json-{{ $hook->id }}" class="hidden bg-gray-50">
                <td colspan="4" class="p-4">
                        <pre class="text-xs bg-black text-green-400 p-4 rounded overflow-x-auto">
{{ json_encode($hook->payload, JSON_PRETTY_PRINT) }}
                        </pre>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <script>
        function toggleJson(id) {
            const row = document.getElementById('json-' + id);
            row.classList.toggle('hidden');
        }
    </script>
</x-admin-layout>

