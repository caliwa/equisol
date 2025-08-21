<div class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:sidebar sticky stashable class="h-full bg-zinc-50 dark:bg-zinc-900 border-r rtl:border-r-0 rtl:border-l border-zinc-200 dark:border-zinc-700">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />
            <flux:brand href="#" logo="https://fluxui.dev/img/demo/logo.png" name="Acme Inc." class="px-2 dark:hidden" />
            <flux:brand href="#" logo="https://fluxui.dev/img/demo/dark-mode-logo.png" name="Acme Inc." class="px-2 hidden dark:flex" />
            <flux:modal.trigger name="search" shortcut="cmd.k">
                <flux:input as="button" placeholder="Search..." icon="magnifying-glass" kbd="⌘K" />
            </flux:modal.trigger>
            <flux:modal name="search" variant="bare" class="w-full max-w-[30rem] my-[12vh] max-h-screen overflow-y-hidden">
                <flux:command class="border-none shadow-lg inline-flex flex-col max-h-[76vh]">
                    <flux:command.input placeholder="Search..." closable />
                    <flux:command.items>
                        <flux:command.item icon="user-plus" kbd="⌘A">Assign to…</flux:command.item>
                        <flux:command.item icon="document-plus">Create new file</flux:command.item>
                        <flux:command.item icon="folder-plus" kbd="⌘⇧N">Create new project</flux:command.item>
                        <flux:command.item icon="book-open">Documentation</flux:command.item>
                        <flux:command.item icon="newspaper">Changelog</flux:command.item>
                        <flux:command.item icon="cog-6-tooth" kbd="⌘,">Settings</flux:command.item>
                    </flux:command.items>
                </flux:command>
            </flux:modal>
            @persist('sidebar')
                <flux:navlist variant="outline">
                    <flux:navlist.item icon="home" href="/menu">Menú</flux:navlist.item>
                    <flux:navlist.item icon="home" href="/gastos">Gastos</flux:navlist.item>
                    <flux:navlist.item icon="inbox" badge="12" href="#">Inbox</flux:navlist.item>
                    <flux:navlist.item icon="document-text" href="#">Documents</flux:navlist.item>
                    <flux:navlist.item icon="calendar" href="#">Calendar</flux:navlist.item>
                    <flux:navlist.group expandable heading="Favorites" class="hidden lg:grid">
                        <flux:navlist.item href="#">Marketing site</flux:navlist.item>
                        <flux:navlist.item href="#">Android app</flux:navlist.item>
                        <flux:navlist.item href="#">Brand guidelines</flux:navlist.item>
                    </flux:navlist.group>
                </flux:navlist>
            @endpersist 
            <flux:spacer />
            <flux:separator />
            <flux:navlist variant="outline">
                <flux:navlist.item wire:click="logout" icon="arrow-left-end-on-rectangle" class="text-red-500!">Salir</flux:navlist.item>
                <flux:navlist.item icon="cog-6-tooth" href="#">Settings</flux:navlist.item>
                <flux:navlist.item icon="information-circle" href="#">Help</flux:navlist.item>
            </flux:navlist>
            <flux:dropdown position="top" align="start" class="max-lg:hidden">
                <flux:profile avatar="https://fluxui.dev/img/demo/user.png" name="Olivia Martin" />
                <flux:menu>
                    <flux:menu.radio.group>
                        <flux:menu.radio checked>Olivia Martin</flux:menu.radio>
                        <flux:menu.radio>Truly Delta</flux:menu.radio>
                    </flux:menu.radio.group>
                </flux:menu>
            </flux:dropdown>
    </flux:sidebar>
</div>