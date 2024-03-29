<?php

namespace App\Http\Controllers;

use App\Http\Requests\TicketRequest;
use App\Models\Log;
use App\Models\Maquina;
use App\Models\Setor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Ticket;
use App\Models\User;
use PDF;
use Database\Seeders\SetorSeeder;

class TicketController extends Controller
{

    // $tickets = Auth::user()->tecnico === 1 ? Ticket::where('status', '<>', '4')->get() : Auth::user()->ticketsRequerente->where('status', '<>', '4');

    // return view('ticket.index', compact('tickets'));


    public function index(Request $request)
    {
        if ($request->filter == "all") {
            $listar = "all";
            $ticket = Ticket::all();
        } elseif ($request->filter == null) {
            $listar = "1";
            $ticket = Ticket::where('status', '1')->get();
        } else {
            $listar = $request->filter;
            $ticket = Ticket::where('status', $request->filter)->get();
        }

        return view('ticket.index', compact('ticket', 'listar'));
    }


    public function create()
    {

        $tecnicos = User::where('tecnico', '<>', '0')->where('status', '1')->get();
        $setoresAtivos = Setor::where('status', '<>', '0')->get();
        $maquinasAtivas = Maquina::where('status', '<>', '0')->get();

        return view('ticket.create', compact('setoresAtivos', 'maquinasAtivas', 'tecnicos'));
    }

    public function store(TicketRequest $request)
    {
        $ticket = Ticket::create([
            'titulo' => $request->titulo,
            'ramal' => $request->ramal,
            'setor_id' => $request->setor_id,
            'maquina_id' => $request->maquina_id,
            'demanda' => $request->demanda,
            'dataAbertura' => $request->dataAbertura,
            'requerente_user_id' => Auth::id(),
            'responsavel_user_id' => Auth::user()->tecnico == 1 ? Auth::id() : null,
        ]);

        // GRAVAR LOG (verificar se for tecnico na hr de criar)

        Log::create([
            'descricao' => 'Iniciou o Ticket',
            'status' => 1,
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('ticket.index')->with('mensagem', 'Sucesso ao cadastrar!');
        // $id = new Ticket;

        // $ticket = Ticket::findOrFail($id)->where('id', $id)->first();

        // // $id_ticket = Ticket::where('id', $ticket);

        // return redirect()->route('ticket.visualizar', ['ticket' => $ticket]);
    }

    public function show($id)
    {
        $ticket = Ticket::findOrFail($id);

        $logs = $ticket->logs->sortDesc();

        return view('ticket.show', compact('ticket', 'logs'));
    }

    public function edit($id)
    {
        $tecnicos = User::where('tecnico', '<>', '0')->where('status', '1')->get();
        $setoresAtivos = Setor::where('status', '<>', '0')->get();
        $maquinasAtivas = Maquina::where('status', '<>', '0')->get();

        $ticket = Ticket::findOrFail($id);
        // return view('ticket.edit', ['ticket' => $ticket]);

        $logs = $ticket->logs->sortDesc();

        return view('ticket.edit', compact('setoresAtivos', 'maquinasAtivas', 'tecnicos', 'ticket', 'logs'));
    }

    public function update(Request $request, $id)
    {

        $ticket = Ticket::findOrFail($id);

        $ticket->update($request->all());

        //LOG

        Log::create([
            'descricao' => $request->obsLog,
            'status' => $request->status,
            'ticket_id' => $id,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('ticket.visualizar', ['id' => $id])->with('mensagem', 'Sucesso ao Editar!');

        // return view('ticket.edit', ['ticket' => $ticket]);
    }

    public function delete($id)
    {
        $ticket = Ticket::findOrFail($id);
        return view('ticket.delete', ['ticket' => $ticket]);
    }

    public function destroy($id)
    {

        $ticketPossuiUsuarios = Ticket::where('tickets_id', $id)->exists();

        if ($ticketPossuiUsuarios) {
            return redirect()->route('ticket.index')->with('error', 'Esse ticket não pode ser excluido, pois existem usuários nesse ticket.');
        }

        $ticket = Ticket::findOrFail($id);
        $ticket->delete();

        return redirect()->route('ticket.index')->with('mensagem', 'Sucesso ao excluir!');
    }

    public function cancel()
    {
        return view('ticket.index');
    }

    public function assumirTicket(Request $request, $id)
    {

        $ticket = Ticket::findOrFail($id)->update([
            'responsavel_user_id' => Auth::id(),
            'status' => '1',
        ]);

        Log::create([
            'descricao' => 'Assumiu o Ticket',
            'status' => '1',
            'ticket_id' => $request->id,
            'user_id' => Auth::id(),
        ]);
        return redirect()->route('ticket.visualizar', $id)->with('mensagem', 'Ticket Assumido com sucesso!');
    }

    public function encerrarTicket(Request $request, $id)
    {
        // if(Auth::id() == 'requerente_user_id' && 'responsavel_user_id' == null){
        //     Ticket::findOrFail($id)->update([
        //         'status' => '4',
        //         'responsavel_user_id' = ''
        //     ]);
        // }

        Ticket::findOrFail($id)->update([
            'status' => '4',
        ]);

        Log::create([
            'descricao' => $request->obsLog,
            'status' => '4',
            'ticket_id' => $id,
            'user_id' => Auth::id(),
        ]);

        // Log::create([
        //     'descricao'=> 'Encerrou o Ticket', 
        //     'status' => '4',
        //     'ticket_id' => $id,
        //     'user_id' => Auth::id(),
        // ]);
        return redirect()->route('ticket.index')->with('mensagem', 'Ticket Encerrado com sucesso!');
    }

    public function report(Request $request)
    {

        $status = "all";
        $maquinas = "all";
        $setores = "all";
        $requerente = "all";
        $responsaveis = "all";
     
        return view('ticket.report', compact('status', 'maquinas', 'setores', 'requerente', 'responsaveis'));
    }

    public function gerarPDF(Request $request)
    {


        $tickets = Ticket::with(['setor']);
        
        if($request->filled('status')){
            $tickets->where('status', $request->status);
        }

        if($request->filled('maquinas')){
            $tickets->where('maquina_id', $request->maquinas);
        }

        if($request->filled('setores')){
            $tickets->where('setor_id', $request->setores);
        }

        if($request->filled('requerente')){
            $tickets->where('requerente_user_id', $request->requerente);
        }

        if($request->filled('representante')){
            $tickets->where('representante_user_id', $request->representante);
        }

        $tickets = $tickets->get();

        // $ticket = Ticket::select('*')->status($request->status)->maquinas($request->maquinas)->setores($request->setores)->requerente($request->requerente)->responsaveis($request->responsaveis)->get();

        $data = [
            'title' => 'Relatório de Tickets',
            'date' => date('H:i d/m/Y'),
            'tickets' => $tickets,
        ];

        $pdf = PDF::loadView('ticket/PDF', $data)->setPaper('A4', 'landscape');;

        return $pdf->download('reportTickets.pdf', compact('tickets'));
    }
}
