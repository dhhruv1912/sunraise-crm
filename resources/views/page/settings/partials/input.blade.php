@php
    $name    = $s->name;
    $label   = $s->label;
    $value   = $s->value ?? $s->default;
    $options = $s->getOptions();
    $attr    = $s->attr ?? '';
@endphp
{{-- @dump($options) --}}
<div class="row">
    <div class="col-md-10">
        @if ($s->type === 0)
            <input type="text"
                   name="{{ $name }}"
                   value="{{ $value }}"
                   class="form-control"
                   {!! $attr !!}>
        
        {{-- ================= NUMBER ================= --}}
        @elseif ($s->type === 1)
            <input type="number"
                   name="{{ $name }}"
                   value="{{ $value }}"
                   class="form-control"
                   {!! $attr !!}>
        
        {{-- ================= SELECT ================= --}}
        @elseif ($s->type === 2)
            <select name="{{ $name }}"
                    class="form-select"
                    {!! $attr !!}>
                @foreach ($options as $key => $opt)
                    <option value="{{ $key }}"
                        @selected((string)$value === (string)$key)>
                        {{ $opt }}
                    </option>
                @endforeach
            </select>
        
        {{-- ================= RADIO ================= --}}
        @elseif ($s->type === 3)
            <div class="row g-1">
                {{-- @foreach ($options as $key => $opt)
                    <div class="col-6">
                        <div class="input-group">
                            <div class="input-group-text">
                                <input type="radio"
                                       name="{{ $name }}"
                                       value="{{ $key }}"
                                       class="form-check-input"
                                       @checked((string)$value === (string)$key)>
                            </div>
                            <label class="input-group-text">
                                {{ $opt }}
                            </label>
                        </div>
                    </div>
                @endforeach --}}
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" name="{{ $name }}" @checked((string)$value === "true") value="true">
                </div>
            </div>
        
        {{-- ================= CHECKBOX ================= --}}
        @elseif ($s->type === 4)
            @php
                $values = is_array($value)
                    ? $value
                    : (json_decode($value, true) ?? []);
            @endphp
        
            <div class="d-flex flex-wrap gap-2 g-1">
                @foreach ($options as $key => $opt)
                    <div class="">
                        <div class="input-group">
                            <div class="input-group-text">
                                <input type="checkbox"
                                       name="{{ $name }}[]"
                                       value="{{ $key }}"
                                       class="form-check-input"
                                       @checked(in_array((string)$key, array_map('strval', $values)))>
                            </div>
                            <label class="input-group-text">
                                {{ $opt }}
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        
        {{-- ================= TEXTAREA ================= --}}
        @elseif ($s->type === 5)
            <textarea name="{{ $name }}"
                      class="form-control"
                      rows="3"
                      {!! $attr !!}>{{ $value }}</textarea>
        
        {{-- ================= FILE ================= --}}
        @elseif ($s->type === 6)
            <input type="file"
                   name="{{ $name }}"
                   class="form-control"
                   {!! $attr !!}>
        
            @if ($value)
                <a href="{{ asset('storage/'.$value) }}"
                   target="_blank"
                   class="small d-block mt-1">
                    View current file
                </a>
            @endif
        
        {{-- ================= DATE ================= --}}
        @elseif ($s->type === 7)
            <input type="date"
                   name="{{ $name }}"
                   value="{{ $value }}"
                   class="form-control"
                   {!! $attr !!}>
        
        {{-- ================= JSON ================= --}}
        @elseif ($s->type === 8)
            @php
                $json = [];
                try {
                    $json = is_array($value)
                        ? $value
                        : (json_decode($value, true) ?? []);
                } catch (\Throwable $e) {}
            @endphp
        
            <div class="border border-2 rounded-3 p-3"
                 style="max-height: 400px; overflow-y:auto">
        
                <div class="json-fields" data-name="{{ $name }}">
                    @foreach ($json as $k => $v)
                        <div class="row mb-2 json-row">
                            <div class="col">
                                <input type="text"
                                       class="form-control json-key"
                                       value="{{ $k }}">
                            </div>
        
                            <div class="col-1 text-center align-self-center">=</div>
        
                            <div class="col">
                                <input type="text"
                                       class="form-control json-value"
                                       value="{{ $v }}">
                            </div>
                        </div>
                    @endforeach
                </div>
        
                <button type="button"
                        class="btn btn-sm btn-outline-primary mt-2"
                        onclick="addJsonRow('{{ $name }}')">
                    Add Key Value Pair
                </button>
        
                {{-- hidden field for submit --}}
                <input type="hidden"
                       name="{{ $name }}"
                       class="json-output">
            </div>
        @endif
    </div>
    <div class="col-md-2">
        <div class="d-flex justify-content-end gap-2 mb-2">
            <button type="button"
                    class="btn btn-xs btn-outline-secondary"
                    data-setting='@json($s)'
                    id="setting-{{ $s->name }}"
                    onclick="editSetting('setting-{{ $s->name }}')">
                    {{-- @json($s) --}}
                <i class="fa-solid fa-pen"></i>
            </button>
        
            <button type="button"
                    class="btn btn-xs btn-outline-danger"
                    onclick="deleteSetting({{ $s->id }})">
                <i class="fa-solid fa-trash"></i>
            </button>
        </div>
    </div>
</div>
<hr>
{{-- ================= TEXT ================= --}}
