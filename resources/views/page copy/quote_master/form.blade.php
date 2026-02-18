@extends('temp.common')

@section('content')

@php $edit = isset($data); @endphp

<div class="card">
    <div class="card-header">
        <h4>{{ $edit ? "Edit Quote Master" : "Add Quote Master" }}</h4>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ $edit ? route('quote_master.update', $data->id) : route('quote_master.store') }}">
            @csrf

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">SKU</label>
                    <input type="text" name="sku" class="form-control" value="{{ old('sku', $data->sku ?? '') }}">
                </div>

                <div class="col-md-8">
                    <label class="form-label">Module (brand & details)</label>
                    <input type="text" name="module" class="form-control" required value="{{ old('module', $data->module ?? '') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">KW</label>
                    <input type="number" step="0.01" name="kw" class="form-control" required value="{{ old('kw', $data->kw ?? '') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Module Count</label>
                    <input type="number" name="module_count" class="form-control" required value="{{ old('module_count', $data->module_count ?? '') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Value</label>
                    <input type="number" step="0.01" name="value" class="form-control" value="{{ old('value', $data->value ?? '') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Taxes</label>
                    <input type="number" step="0.01" name="taxes" class="form-control" value="{{ old('taxes', $data->taxes ?? '') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Metering Cost</label>
                    <input type="number" step="0.01" name="metering_cost" class="form-control" value="{{ old('metering_cost', $data->metering_cost ?? '') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">MCB / PPA</label>
                    <input type="number" step="0.01" name="mcb_ppa" class="form-control" value="{{ old('mcb_ppa', $data->mcb_ppa ?? '') }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Payable</label>
                    <input type="number" step="0.01" name="payable" class="form-control" value="{{ old('payable', $data->payable ?? '') }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Subsidy</label>
                    <input type="number" step="0.01" name="subsidy" class="form-control" value="{{ old('subsidy', $data->subsidy ?? '') }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Projected</label>
                    <input type="number" step="0.01" name="projected" class="form-control" readonly value="{{ old('projected', $data->projected ?? '') }}">
                </div>
            </div>

            <hr class="my-3">

            <div class="alert alert-info">
                <strong>Auto Pricing Calculator</strong><br>
                Value, Taxes, Payable & Projected will auto-update as you change KW, metering cost or MCB/PPA.
            </div>

            <h5>Meta (Custom key/value)</h5>
            <div id="metaContainer">
                @if(old('meta_key'))
                    @foreach(old('meta_key') as $i => $k)
                        <div class="row mb-2 metaRow">
                            <div class="col-md-5"><input type="text" name="meta_key[]" class="form-control" value="{{ $k }}"></div>
                            <div class="col-md-5"><input type="text" name="meta_value[]" class="form-control" value="{{ old('meta_value')[$i] ?? '' }}"></div>
                            <div class="col-md-2"><button class="btn btn-danger w-100" type="button" onclick="this.closest('.metaRow').remove()">X</button></div>
                        </div>
                    @endforeach
                @elseif($edit && is_array($data->meta))
                    @foreach($data->meta as $k => $v)
                        <div class="row mb-2 metaRow">
                            <div class="col-md-5"><input type="text" name="meta_key[]" class="form-control" value="{{ $k }}"></div>
                            <div class="col-md-5"><input type="text" name="meta_value[]" class="form-control" value="{{ $v }}"></div>
                            <div class="col-md-2"><button class="btn btn-danger w-100" type="button" onclick="this.closest('.metaRow').remove()">X</button></div>
                        </div>
                    @endforeach
                @endif
            </div>

            <div class="mt-2 mb-3">
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addMetaRow()">Add Meta Row</button>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-success">{{ $edit ? 'Update' : 'Create' }}</button>
                <a href="{{ route('quote_master.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Auto SKU generator and calculator logic
    document.addEventListener('DOMContentLoaded', () => {
        const fields = ['module','kw','module_count','metering_cost','mcb_ppa','payable','subsidy'];

        fields.forEach(name => {
            const el = document.querySelector("[name='"+name+"']");
            if (el) el.addEventListener('input', () => {
                autoGenerateSKU();
                advancedCalc();
            });
        });

        // run once to initialize
        autoGenerateSKU();
        advancedCalc();
    });

    function autoGenerateSKU() {
        const module = document.querySelector("[name='module']")?.value?.trim() ?? '';
        const kw = document.querySelector("[name='kw']")?.value?.trim() ?? '';
        const mc = document.querySelector("[name='module_count']")?.value?.trim() ?? '';
        if (!module || !kw || !mc) return;
        const brand = module.split(' ')[0].replace(/\//g,'-');
        document.querySelector("[name='sku']").value = `${brand}-${kw}-MC-${mc}`;
    }

    function advancedCalc() {
        const kw = parseFloat(document.querySelector("[name='kw']")?.value) || 0;
        // default rate per kW (you can change or make dynamic)
        const ratePerKw = 48000;
        const value = kw * ratePerKw;
        const taxes = value * 0.12;

        const metering = parseFloat(document.querySelector("[name='metering_cost']")?.value) || 0;
        const mcb = parseFloat(document.querySelector("[name='mcb_ppa']")?.value) || 0;

        const payable = value + taxes + metering + mcb;
        document.querySelector("[name='value']").value = value ? value.toFixed(2) : '';
        document.querySelector("[name='taxes']").value = taxes ? taxes.toFixed(2) : '';
        document.querySelector("[name='payable']").value = payable ? payable.toFixed(2) : '';

        autoProjectedCalc();
    }

    function autoProjectedCalc() {
        const payable = parseFloat(document.querySelector("[name='payable']")?.value) || 0;
        const subsidy = parseFloat(document.querySelector("[name='subsidy']")?.value) || 0;
        const projected = payable - subsidy;
        document.querySelector("[name='projected']").value = projected ? projected.toFixed(2) : '';
    }

    function addMetaRow() {
        const html = `
            <div class="row mb-2 metaRow">
                <div class="col-md-5"><input type="text" name="meta_key[]" class="form-control" placeholder="Key"></div>
                <div class="col-md-5"><input type="text" name="meta_value[]" class="form-control" placeholder="Value"></div>
                <div class="col-md-2"><button class="btn btn-danger w-100" type="button" onclick="this.closest('.metaRow').remove()">X</button></div>
            </div>
        `;
        document.getElementById('metaContainer').insertAdjacentHTML('beforeend', html);
    }
</script>
@endsection
