@php
    // URL file FINAL (tidak tergantung DB path)
    $fileUrl = asset('storage/attachments/' . $filename);
@endphp

<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between flex-column flex-sm-row">
            <div>
                <h5 class="card-title mb-0 text-uppercase">{{ $extension }}</h5>
                <small>
                    @if ($letter->type == 'incoming')
                        <a href="{{ route('transaction.incoming.show', $letter) }}" class="fw-bold">
                            {{ $letter->reference_number }}
                        </a>
                    @else
                        <a href="{{ route('transaction.outgoing.show', $letter) }}" class="fw-bold">
                            {{ $letter->reference_number }}
                        </a>
                    @endif
                </small>
            </div>

            {{-- ICON --}}
            @if (strtolower($extension) == 'pdf')
                <i class="bx bxs-file-pdf display-5 text-danger"></i>
            @elseif(strtolower($extension) == 'png')
                <i class="bx bxs-file-png display-5 text-info"></i>
            @elseif(in_array(strtolower($extension), ['jpeg', 'jpg']))
                <i class="bx bxs-file-jpg display-5 text-warning"></i>
            @else
                <i class="bx bxs-file display-5"></i>
            @endif
        </div>

        <div class="accordion mt-3" id="accordion-{{ str_replace('.', '-', $filename) }}">
            <div class="accordion-item card">
                <button type="button" class="accordion-button collapsed d-flex align-items-center"
                    data-bs-toggle="collapse" data-bs-target="#accordion-id-{{ str_replace('.', '-', $filename) }}">

                    <div class="text-truncate w-100" title="{{ $filename }}">
                        {{ $filename }}
                    </div>
                </button>



                <div id="accordion-id-{{ str_replace('.', '-', $filename) }}"
                    class="accordion-collapse collapse text-center"
                    data-bs-parent="#accordion-{{ str_replace('.', '-', $filename) }}">
                    @if (strtolower($extension) == 'pdf')
                        <a class="btn my-3 btn-primary" href="{{ $fileUrl }}" download>
                            {{ __('menu.general.download') }}
                        </a>
                    @elseif(in_array(strtolower($extension), ['jpg', 'jpeg', 'png']))
                        <img src="{{ $fileUrl }}" class="img-fluid" alt="Picture">
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
