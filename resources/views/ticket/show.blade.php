@extends('layouts.master')

@section('title', 'Ticket - Técnico')

@section('page')



<div class="container" style="margin-top: 15px;margin-bottom: 10px;">
    <div class="row mt-4 mb-2" width="100%">
        <p style="font-size: 32px;font-weight: bold;">#{{ old('id', $ticket->id) }} - {{ old('titulo', $ticket->titulo)}} </p>

        @include('layouts.mensagem')

        <div class="col-md-6">

            <div>
                <div class="row row-cols-1">

                    <div class="col d-flex mb-1 align-items-center">
                        <p style="font-weight: bold;">Requerente:&nbsp;</p>
                        <p style="color: rgb(117,117,117);">{{ old('name', $ticket->requerente->name) }} [ {{ old('email', $ticket->requerente->email) }} ] </p>
                    </div>

                    <div class="col d-flex  mb-1">
                        <p style="font-weight: bold;">Ramal:&nbsp;&nbsp;</p>
                        <p style="color: rgb(117,117,117);">{{ old('ramal', $ticket->ramal)}}</p>
                    </div>
                    <div class="col d-flex  mb-1">
                        <p style="font-weight: bold;">Setor:&nbsp;&nbsp;</p>
                        <p style="color: rgb(117,117,117);">{{ old('setor', $ticket->setor->name)}}</p>
                    </div>
                    <div class="col d-flex  mb-1">
                        <p style="font-weight: bold;">Máquina:&nbsp;&nbsp;</p>
                        <p style="color: rgb(117,117,117);">{{ old('maquina', $ticket->maquina->name)}}</p>
                    </div>


                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div>
                <div class="row row-cols-1">

                    <div class="col d-flex mb-1">
                        <p style="font-weight: bold;">Data de Abertura:&nbsp;&nbsp;</p>
                        <p style="color: rgb(117,117,117);">{{ old('created_at', $ticket->created_at)}}</p>
                    </div>

                    <div class="col d-flex align-items-center mb-1">
                        <p style="font-weight: bold;">Status:&nbsp;&nbsp;</p>
                        <p style="color: rgb(117,117,117);">
                            @if( $ticket->status == '1')
                            Aberto
                            @elseif ($ticket->status == '2')
                            Em Atendimento
                            @elseif ($ticket->status == '3')
                            Aguardando Terceiros
                            @elseif ($ticket->status == '4')
                            Encerrado
                            @endif</p>
                        <!-- <select required class="form-control bg-light border rounded shadow-sm p-2 " name="status">
                            <option value="{{old('status', $ticket->status)}}">
                                @if( $ticket->status === '1')
                                Aberto
                                @elseif ($ticket->status === '2')
                                Em Atendimento
                                @elseif ($ticket->status === '3')
                                Aguardando Terceiros
                                @elseif ($ticket->status === '0')
                                Encerrado
                                @endif
                            </option>
                            <option value="1">Aberto</option>
                            <option value="2">Em Atendimento</option>
                            <option value="3">Aguardando Terceiros</option>
                            <option value="0">Encerrado</option>
                        </select> -->
                    </div>

                    <div class="col d-flex align-items-center">
                        <p style="font-weight: bold;">Técnico:&nbsp;&nbsp;</p>
                        <p style="color: rgb(117,117,117);">

                            @if ($ticket->responsavel == null && $ticket->status == '4')
                            Encerrado pelo requerente.
                            @elseif ($ticket->responsavel === null)
                            Aguardando...
                            @else
                            {{ old('name', $ticket->responsavel->name)}} [ {{ old('email', $ticket->responsavel->email)}} ]
                            @endif

                        </p>

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-md-6">
            <p style="margin-bottom:7px; font-weight: bold;">Descrição:</p>
            <textarea class="bg-light border rounded shadow-sm mb-4 p-2" style="width: 100%;min-height: 180px;" disabled="" readonly="">{{ old('demanda', $ticket->demanda)}}</textarea>
        </div>

        <div class="col-md-6">
            <p style="margin-bottom: 5px;font-weight: bold;">Atividades:</p>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Editor</th>
                            <th>Data / Hora</th>
                            <th>Descrição</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($logs as $log)
                        <tr>
                            <td>{{ $log->usuario->name }}</td>
                            <td>{{ $log->created_at }}</td>
                            <td>{{ $log->descricao }}</td>
                            <td> @if( $log->status == '1')
                                Aberto
                                @elseif ($log->status == '2')
                                Em Atendimento
                                @elseif ($log->status == '3')
                                Aguardando Terceiros
                                @elseif ($log->status == '4')
                                Encerrado
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
<div class="modal fade" role="dialog" tabindex="-1" id="modal-delete-{{ $ticket->id }}">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="margin-right: 31px;">Deseja realmente encerrar?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('ticket.encerrar-ticket', ['id'=>$ticket->id]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body text-start">
                    <span>ID: {{ $ticket->id }}</span></br>
                    <span>Nome: {{ $ticket->titulo }}</span>
                    </br></br>
                    <div class="col">
                        <p style="margin-bottom:0px;">Observações&nbsp;no&nbsp;Log:&nbsp;</p>
                        <textarea class="form-control bg-light border rounded shadow-sm p-2 mb-4" style="width: 100%;min-height: 180px;height:80%;" name="obsLog" placeholder="Motivo de encerramento" require value="{{ old('obsLog') }}"></textarea>

                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-center">
                    <button class="btn btn-secondary text-light" type="button" data-bs-dismiss="modal">Voltar</button>

                    <button class="btn btn-danger" type="submit">Encerrar</button>
            </form>
        </div>
    </div>
</div>
</div>
<div class="container">
    <div class="col-md-6">

        @if ($ticket->status != '4')
        @if(Auth::check() && Auth::user()->tecnico == '1' || Auth::user()->id === $ticket->requerente_user_id)
        <a class="btn btn-warning m-1" role="button" href="{{ route('edit.ticket', ['id'=>$ticket->id]) }}" style="color:white;">Editar</a>
        @if(Auth::check() && Auth::user()->tecnico == '1' && $ticket->responsavel_user_id === null)
        <a class="btn btn-success m-1" role="button" href="{{ route('ticket.assumir-ticket', ['id'=>$ticket->id]) }}">Assumir</a>
        @endif
        <a class="btn btn-danger m-1" role="button" data-bs-target="#modal-delete-{{ $ticket->id }}" data-bs-toggle="modal">Encerrar</a>
        <!-- <a class="btn btn-danger m-1" role="button" href="{{ route('ticket.encerrar-ticket', ['id'=>$ticket->id]) }}">Encerrar</a> -->
        @endif
        @endif

        <a class="btn btn-secondary m-1" role="button" href="{{ route('ticket.index') }}">Voltar</a>

    </div>
</div>
@endsection