<?php

namespace App\Http\Controllers\Admin;

use App\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreInvitationsRequest;
use App\Http\Requests\Admin\UpdateInvitationsRequest;

class InvitationsController extends Controller
{
    /**
     * Display a listing of Invitation.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('invitation_access')) {
            return abort(401);
        }


        if (request('show_deleted') == 1) {
            if (! Gate::allows('invitation_delete')) {
                return abort(401);
            }
            $invitations = Invitation::onlyTrashed()->get();
        } else {
            $invitations = Invitation::all();
        }

        return view('admin.invitations.index', compact('invitations'));
    }

    /**
     * Show the form for creating new Invitation.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('invitation_create')) {
            return abort(401);
        }
        
        $events = \App\Event::get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');

        return view('admin.invitations.create', compact('events'));
    }

    /**
     * Store a newly created Invitation in storage.
     *
     * @param  \App\Http\Requests\StoreInvitationsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreInvitationsRequest $request)
    {
        if (! Gate::allows('invitation_create')) {
            return abort(401);
        }
        $invitation = Invitation::create($request->all());



        return redirect()->route('admin.invitations.index');
    }


    /**
     * Show the form for editing Invitation.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('invitation_edit')) {
            return abort(401);
        }
        
        $events = \App\Event::get()->pluck('name', 'id')->prepend(trans('quickadmin.qa_please_select'), '');

        $invitation = Invitation::findOrFail($id);

        return view('admin.invitations.edit', compact('invitation', 'events'));
    }

    /**
     * Update Invitation in storage.
     *
     * @param  \App\Http\Requests\UpdateInvitationsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateInvitationsRequest $request, $id)
    {
        if (! Gate::allows('invitation_edit')) {
            return abort(401);
        }
        $invitation = Invitation::findOrFail($id);
        $invitation->update($request->all());



        return redirect()->route('admin.invitations.index');
    }


    /**
     * Display Invitation.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('invitation_view')) {
            return abort(401);
        }
        $invitation = Invitation::findOrFail($id);

        return view('admin.invitations.show', compact('invitation'));
    }


    /**
     * Remove Invitation from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Gate::allows('invitation_delete')) {
            return abort(401);
        }
        $invitation = Invitation::findOrFail($id);
        $invitation->delete();

        return redirect()->route('admin.invitations.index');
    }

    /**
     * Delete all selected Invitation at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('invitation_delete')) {
            return abort(401);
        }
        if ($request->input('ids')) {
            $entries = Invitation::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
    }


    /**
     * Restore Invitation from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        if (! Gate::allows('invitation_delete')) {
            return abort(401);
        }
        $invitation = Invitation::onlyTrashed()->findOrFail($id);
        $invitation->restore();

        return redirect()->route('admin.invitations.index');
    }

    /**
     * Permanently delete Invitation from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function perma_del($id)
    {
        if (! Gate::allows('invitation_delete')) {
            return abort(401);
        }
        $invitation = Invitation::onlyTrashed()->findOrFail($id);
        $invitation->forceDelete();

        return redirect()->route('admin.invitations.index');
    }
}
