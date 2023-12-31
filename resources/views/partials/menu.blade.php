<div id="sidebar" class="c-sidebar c-sidebar-fixed c-sidebar-lg-show">

    <div class="c-sidebar-brand d-md-down-none">
        <a class="c-sidebar-brand-full h4" href="#">
            {{ trans('panel.site_title') }}
        </a>
    </div>

    <ul class="c-sidebar-nav">
        <li class="c-sidebar-nav-item">
            <a href="{{ route("admin.home") }}" class="c-sidebar-nav-link">
                <i class="c-sidebar-nav-icon fas fa-fw fa-tachometer-alt">

                </i>
                {{ trans('global.dashboard') }}
            </a>
        </li>
        @can('user_management_access')
            <li class="c-sidebar-nav-dropdown {{ request()->is("admin/permissions*") ? "c-show" : "" }} {{ request()->is("admin/roles*") ? "c-show" : "" }} {{ request()->is("admin/users*") ? "c-show" : "" }}">
                <a class="c-sidebar-nav-dropdown-toggle" href="#">
                    <i class="fa-fw fas fa-users c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.userManagement.title') }}
                </a>
                <ul class="c-sidebar-nav-dropdown-items">
                    @can('permission_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.permissions.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/permissions") || request()->is("admin/permissions/*") ? "c-active" : "" }}">
                                <i class="fa-fw fas fa-unlock-alt c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.permission.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('role_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.roles.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/roles") || request()->is("admin/roles/*") ? "c-active" : "" }}">
                                <i class="fa-fw fas fa-briefcase c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.role.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('user_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.users.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/users") || request()->is("admin/users/*") ? "c-active" : "" }}">
                                <i class="fa-fw fas fa-user c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.user.title') }}
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcan
        @can('openai_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route("admin.openais.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/openais") || request()->is("admin/openais/*") ? "c-active" : "" }}">
                    <i class="fa-fw fas fa-desktop c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.openai.title') }}
                </a>
            </li>
        @endcan
        @can('project_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route("admin.projects.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/projects") || request()->is("admin/projects/*") ? "c-active" : "" }}">
                    <i class="fa-fw fas fa-project-diagram c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.project.title') }}
                </a>
            </li>
        @endcan
        @can('assistant_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route("admin.assistants.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/assistants") || request()->is("admin/assistants/*") ? "c-active" : "" }}">
                    <i class="fa-fw fas fa-robot c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.assistant.title') }}
                </a>
            </li>
        @endcan
        @can('instruction_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route("admin.instructions.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/instructions") || request()->is("admin/instructions/*") ? "c-active" : "" }}">
                    <i class="fa-fw fas fa-terminal c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.instruction.title') }}
                </a>
            </li>
        @endcan
        @can('training_access')
            <li class="c-sidebar-nav-item">
                <a href="{{ route("admin.trainings.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/trainings") || request()->is("admin/trainings/*") ? "c-active" : "" }}">
                    <i class="fa-fw fas fa-graduation-cap c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.training.title') }}
                </a>
            </li>
        @endcan
        @can('log_menu_access')
            <li class="c-sidebar-nav-dropdown {{ request()->is("admin/logs*") ? "c-show" : "" }} {{ request()->is("admin/log-messages*") ? "c-show" : "" }}">
                <a class="c-sidebar-nav-dropdown-toggle" href="#">
                    <i class="fa-fw far fa-comments c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.logMenu.title') }}
                </a>
                <ul class="c-sidebar-nav-dropdown-items">
                    @can('log_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.logs.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/logs") || request()->is("admin/logs/*") ? "c-active" : "" }}">
                                <i class="fa-fw far fa-comments c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.log.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('log_message_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.log-messages.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/log-messages") || request()->is("admin/log-messages/*") ? "c-active" : "" }}">
                                <i class="fa-fw far fa-comment c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.logMessage.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('log_history_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.log-histories.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/log-histories") || request()->is("admin/log-histories/*") ? "c-active" : "" }}">
                                <i class="fa-fw fas fa-history c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.logHistory.title') }}
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcan
        @if(file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php')))
            @can('profile_password_edit')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link {{ request()->is('profile/password') || request()->is('profile/password/*') ? 'c-active' : '' }}" href="{{ route('profile.password.edit') }}">
                        <i class="fa-fw fas fa-key c-sidebar-nav-icon">
                        </i>
                        {{ trans('global.change_password') }}
                    </a>
                </li>
            @endcan
        @endif
        <li class="c-sidebar-nav-item">
            <a href="#" class="c-sidebar-nav-link" onclick="event.preventDefault(); document.getElementById('logoutform').submit();">
                <i class="c-sidebar-nav-icon fas fa-fw fa-sign-out-alt">

                </i>
                {{ trans('global.logout') }}
            </a>
        </li>
    </ul>

</div>