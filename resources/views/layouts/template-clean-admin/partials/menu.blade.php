<style>
   @media(max-width:1025px) {
        .d-lg-block {
            display: none !important;
        }
    }
</style>

<ul class="main-menu no_print">
    <li class="sub-header d-none d-sm-none d-md-none d-lg-block d-xl-block">
      <span >Menú principal</span>
    </li>

    @canany(['gestionar_usuarios','gestionar_roles','gestionar_permisos','gestionar_sucursales'])
        <li class="selected has-sub-menu">
            <a href="#">
                <div class="icon-w">
                    <div class="fa fa-cogs"></div>
                </div>
                <span>Administración</span>
            </a>
            <div class="sub-menu-w">
                <div class="sub-menu-header d-none d-sm-none d-md-none d-lg-block d-xl-block">
                    Administración
                </div>
                <div class="sub-menu-i">
                    <ul class="sub-menu">
                        @can(['gestionar_usuarios'])
                            <li>
                                <a href="{{ route('admin.usuarios.index') }}">Usuarios</a>
                            </li>
                        @endcan

                        @can(['gestionar_roles'])
                            <li>
                                <a href="{{ route('admin.roles.index') }}">Roles</a>
                            </li>
                        @endcan

                        @can(['gestionar_permisos'])
                            <li>
                                <a href="{{ route('admin.permisos.index') }}">Permisos</a>
                            </li>
                        @endcan

                        @can(['gestionar_sucursales'])
                            <li>
                                <a href="{{ route('admin.sucursales.index') }}">Sucursal</a>
                            </li>
                        @endcan
                    </ul>
                </div>
            </div>
        </li>
    @endcanany

    @can('listar_alumnos')
    <li class="">
        <a href="{{ route('alumnos.index') }}">
            <div class="icon-w">
            <div class="fa fa-users"></div>
            </div>
            <span>Alumnos</span></a>
    </li>
    @endcan

    @can('listar_materias')
        <li class="">
            <a href="{{ route('materias.index') }}">
                <div class="icon-w">
                <div class="fa fa-book"></div>
                </div>
                <span>Materias</span></a>
        </li>
    @endcan

    <li class="sub-header d-none d-sm-none d-md-none d-lg-block d-xl-block">
        <span>Opciones</span>
    </li>

    <li class="">
        <a href="{{ route('soporte.index') }}">
            <div class="icon-w">
            <div class="fa fa-headphones"></div>
            </div>
            <span>Soporte Técnico</span></a>
    </li>

    <li class=" ">
        <a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
        <div class="icon-w">
            <div class="os-icon os-icon-signs-11"></div>
        </div>
        <span>Cerrar Sesión</span></a>
    </li>
</ul>
