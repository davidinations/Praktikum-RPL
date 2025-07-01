@extends('layouts.admin')

@section('title', 'Laptop Details - Laptop Store')

@section('admin-content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="bi bi-eye"></i> Laptop Details</h4>
        <div>
            <a href="{{ route('admin.laptops.edit', $laptop->id_laptop) }}" class="btn btn-warning me-2">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <a href="{{ route('admin.laptops.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th width="30%">ID:</th>
                            <td>{{ $laptop->id_laptop }}</td>
                        </tr>
                        <tr>
                            <th>Brand:</th>
                            <td>{{ $laptop->merek }}</td>
                        </tr>
                        <tr>
                            <th>Model:</th>
                            <td>{{ $laptop->model }}</td>
                        </tr>
                        <tr>
                            <th>Price:</th>
                            <td>Rp {{ number_format($laptop->harga, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Processor:</th>
                            <td>{{ $laptop->processor }}</td>
                        </tr>
                        <tr>
                            <th>RAM:</th>
                            <td>{{ $laptop->ram }} GB</td>
                        </tr>
                        <tr>
                            <th>Storage:</th>
                            <td>{{ $laptop->storage }} GB</td>
                        </tr>
                        <tr>
                            <th>GPU:</th>
                            <td>{{ $laptop->gpu }}</td>
                        </tr>
                        <tr>
                            <th>Battery:</th>
                            <td>{{ $laptop->ukuran_baterai }} mAh</td>
                        </tr>
                        <tr>
                            <th>Created By:</th>
                            <td>{{ $laptop->admin ? $laptop->admin->username_admin : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Created At:</th>
                            <td>{{ $laptop->created_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Updated At:</th>
                            <td>{{ $laptop->updated_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    @if($laptop->gambar)
                        <div class="text-center">
                            <img src="{{ asset('storage/laptops/' . $laptop->gambar) }}" alt="{{ $laptop->merek }} {{ $laptop->model }}" class="img-fluid rounded" style="max-height: 300px;">
                        </div>
                    @else
                        <div class="text-center text-muted">
                            <i class="bi bi-laptop" style="font-size: 5rem;"></i>
                            <p>No image available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
