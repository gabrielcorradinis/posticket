@extends('layouts.master')

@section('title', 'Setores')

@section('page')
<div class="container" style="margin-top: 15px;margin-bottom: 10px;">

    <div class="row">
        <div class="col-md-12" style="margin-top: 10px;">
            <p style="font-size: 32px;font-weight: bold;">Lista de Setores</p>
        </div>
    </div>

    @if(Auth::check() && Auth::user()->tecnico == 1)
    <div class="row">
        <div class="col">
            <a class="btn btn-primary" role="button" href="{{ route('cadastrar.setor') }}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Cadastrar Novo Setor&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
        </div>
    </div>
    @endif

    @include('layouts.mensagem')

    <div class="row">
        <div class="col" style="margin-top: 10px;">

            <div class="d-flex align-items-center mt-3">
                <p style="margin-top: 5px;">Filtrar por:&nbsp;&nbsp;</p>

                <form action=" {{ route('setor.index') }} " method="get">
                    <div class="dropdown ">
                        <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                            &nbsp;&nbsp;
                            @if($listar == 'all')
                            Todos
                            @elseif($listar == '1')
                            Ativos
                            @else
                            Inativos
                            @endif
                            &nbsp;&nbsp;
                        </button>
                        <ul class="dropdown-menu">
                            <li><button type="submit" value="all" name="filter" class="dropdown-item">Todos</button></li>
                            <li><button type="submit" value="1" name="filter" class="dropdown-item">Ativos</button></li>
                            <li><button type="submit" value="0" name="filter" class="dropdown-item">Inativos</button></li>
                        </ul>
                    </div>
                </form>
            </div>
            <!-- <div class="mb-3 btn-group">
                    <button type="submit" value="all" name="filter" class="btn btn-primary">Todos</button>

                    <button type="submit" value="1" name="filter" class="btn-primary btn">Ativos</button>

                    <button type="submit" value="0" name="filter" class="btn-primary btn">Inativos</button>
                </div> -->


            <div class="table-responsive">
                <table id="tabela" class="table table-striped display">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nome</th>
                            <th>Status</th>
                            @if(Auth::check() && Auth::user()->tecnico === 1)
                            <th class="text-end"></th>
                            @endif

                        </tr>
                    </thead>
                    <tbody>

                        @foreach($setores as $setor)
                        <div class="modal fade" role="dialog" tabindex="-1" id="modal-delete-{{ $setor->id }}">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" style="margin-right: 31px;">Deseja realmente excluir?</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-start">
                                        <span>Código: {{ $setor->id }}</span></br>
                                        <span>Nome: {{ $setor->name }}</span>
                                        </br>
                                        <small>Por motivos de segurança, esses dados serão desativados.</small>
                                    </div>
                                    <div class="modal-footer d-flex justify-content-center">
                                        <button class="btn btn-primary text-light" type="button" data-bs-dismiss="modal">Voltar</button>
                                        <form action="{{ route('desativa.setor', $setor->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <button class="btn btn-warning" type="submit">Desativar</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" role="dialog" tabindex="-1" id="modal-active-{{ $setor->id }}">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" style="margin-right: 31px;">Deseja realmente reativar?</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-start">
                                        <span>Código: {{ $setor->id }}</span></br>
                                        <span>Nome: {{ $setor->name }}</span>
                                    </div>
                                    <div class="modal-footer d-flex justify-content-center">
                                        <button class="btn btn-primary text-light" type="button" data-bs-dismiss="modal">Voltar</button>
                                        <form action="{{ route('ativa.setor', $setor->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <button class="btn btn-warning" type="submit">Ativar</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <tr>
                            <td>{{$setor->id}}</td>
                            <td>{{$setor->name}}</td>

                            <td>@if($setor->status == 1)
                                Ativo
                                @else
                                Inativo
                                @endif
                            </td>

                            @if(Auth::check() && Auth::user()->tecnico == '1')
                            <td class="text-end">
                                <div class="dropdown">
                                    <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                                        Ações
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li> <a class="dropdown-item" role="button" href="{{ route('edit.setor', $setor->id )}} ">Editar</a></li>

                                        @if($setor->status == '1')
                                        <li><a class="dropdown-item" type="button" data-bs-target="#modal-delete-{{ $setor->id }}" data-bs-toggle="modal">Excluir</a></li>
                                        @else

                                        <li><a class="dropdown-item" type="button" data-bs-target="#modal-active-{{ $setor->id }}" data-bs-toggle="modal">&nbsp;Ativar&nbsp;</a></li>
                                        @endif

                                    </ul>
                                </div>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection


<script>
    $('.dropdown-toggle').dropdown();
</script>