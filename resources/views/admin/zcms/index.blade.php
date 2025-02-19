@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('cruds.zcm.title') }}
    </div>

    <div class="card-body">
        <div class="row">
            <div class="offset-4 col-md-4">
                <form action="/admin/zcms/orders" method="POST" id="zcm_orders">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Data de início</label>
                                <input type="date" class="form-control" name="start_date" id="start_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Data de fim</label>
                                <input type="date" class="form-control" name="end_date" id="end_date" required>
                            </div>
                        </div>
                        <div class="offset-3 col-md-6">
                            <button type="submit" class="btn btn-primary btn-block">Obter dados</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


@endsection
@section('scripts')
<script>

</script>
@endsection
