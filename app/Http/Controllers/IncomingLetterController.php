<?php

namespace App\Http\Controllers;

use App\Enums\LetterType;
use App\Http\Requests\StoreLetterRequest;
use App\Http\Requests\UpdateLetterRequest;
use App\Models\Attachment;
use App\Models\Classification;
use App\Models\Config;
use App\Models\Letter;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class IncomingLetterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        return view('pages.transaction.incoming.index', [
            'data'   => Letter::incoming()->render($request->search),
            'search' => $request->search,
        ]);
    }

    /**
     * Display incoming letter agenda.
     */
    public function agenda(Request $request): View
    {
        return view('pages.transaction.incoming.agenda', [
            'data'   => Letter::incoming()
                ->agenda($request->since, $request->until, $request->filter)
                ->render($request->search),
            'search' => $request->search,
            'since'  => $request->since,
            'until'  => $request->until,
            'filter' => $request->filter,
            'query'  => $request->getQueryString(),
        ]);
    }

    /**
     * Print agenda.
     */
    public function print(Request $request): View
    {
        $agenda = __('menu.agenda.menu');
        $letter = __('menu.agenda.incoming_letter');
        $title  = App::getLocale() === 'id'
            ? "$agenda $letter"
            : "$letter $agenda";

        return view('pages.transaction.incoming.print', [
            'data'   => Letter::incoming()
                ->agenda($request->since, $request->until, $request->filter)
                ->get(),
            'search' => $request->search,
            'since'  => $request->since,
            'until'  => $request->until,
            'filter' => $request->filter,
            'config' => Config::pluck('value', 'code')->toArray(),
            'title'  => $title,
        ]);
    }

    /**
     * Show create form.
     */
    public function create(): View
    {
        return view('pages.transaction.incoming.create', [
            'classifications' => Classification::all(),
        ]);
    }

    /**
     * Store new incoming letter.
     */
    public function store(StoreLetterRequest $request): RedirectResponse
    {
        try {
            if ($request->type !== LetterType::INCOMING->type()) {
                throw new \Exception(__('menu.transaction.incoming_letter'));
            }

            $validated = $request->validated();

            // Ensure bidang assignment: non-admin users should have letter in their own bidang
            if (auth()->user()->role != \App\Enums\Role::ADMIN->status()) {
                $validated['bidang_id'] = auth()->user()->bidang_id;
            }

            $letter = Letter::create(
                array_merge(
                    $validated,
                    ['user_id' => auth()->id()]
                )
            );

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {

                    $extension = strtolower($file->getClientOriginalExtension());

                    if (!in_array($extension, ['pdf', 'jpg', 'jpeg', 'png'])) {
                        continue;
                    }

                    // 🔑 NAMA FILE AMAN & JELAS
                    $filename = time() . '-' . str_replace(
                        ' ',
                        '-',
                        $file->getClientOriginalName()
                    );

                    // SIMPAN KE storage/app/public/attachments
                    $file->storeAs('attachments', $filename, 'public');

                    Attachment::create([
                        'filename'      => $filename,
                        'original_name' => $file->getClientOriginalName(),
                        'extension'     => $extension,
                        'user_id'       => auth()->id(),
                        'letter_id'     => $letter->id,
                    ]);
                }
            }

            return redirect()
                ->route('transaction.incoming.index')
                ->with('success', __('menu.general.success'));
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display specific letter.
     */
    public function show(Letter $incoming): View
    {
        return view('pages.transaction.incoming.show', [
            'data' => $incoming->load(['classification', 'user', 'attachments']),
        ]);
    }

    /**
     * Edit letter.
     */
    public function edit(Letter $incoming): View
    {
        return view('pages.transaction.incoming.edit', [
            'data'            => $incoming,
            'classifications' => Classification::all(),
        ]);
    }

    /**
     * Update letter.
     */
    public function update(UpdateLetterRequest $request, Letter $incoming): RedirectResponse
    {
        try {
            $validated = $request->validated();
            if (auth()->user()->role != \App\Enums\Role::ADMIN->status()) {
                // non-admins cannot change bidang
                unset($validated['bidang_id']);
            }
            $incoming->update($validated);

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {

                    $extension = strtolower($file->getClientOriginalExtension());

                    if (!in_array($extension, ['pdf', 'jpg', 'jpeg', 'png'])) {
                        continue;
                    }

                    $filename = time() . '-' . str_replace(
                        ' ',
                        '-',
                        $file->getClientOriginalName()
                    );

                    $file->storeAs('attachments', $filename, 'public');

                    Attachment::create([
                        'filename'      => $filename,
                        'original_name' => $file->getClientOriginalName(),
                        'extension'     => $extension,
                        'user_id'       => auth()->id(),
                        'letter_id'     => $incoming->id,
                    ]);
                }
            }

            return back()->with('success', __('menu.general.success'));
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Delete letter.
     */
    public function destroy(Letter $incoming): RedirectResponse
    {
        try {
            $incoming->delete();

            return redirect()
                ->route('transaction.incoming.index')
                ->with('success', __('menu.general.success'));
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
