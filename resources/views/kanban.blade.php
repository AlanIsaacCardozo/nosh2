<div class="bg-gray-100 mb-10 antialiased leading-none">
    <div id="app" class="row">
        <div class="md:mx-4 relative overflow-hidden">
            <main class="h-full flex flex-col overflow-auto">
                <kanban-board :initial-data="{{ json_encode($tasks) }}"></kanban-board>
            </main>
        </div>
    </div>
</div>
