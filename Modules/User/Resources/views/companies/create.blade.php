@extends('layouts.app')

@section('title', 'Create Companies')

@section('third_party_stylesheets')
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet" />
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
@endsection

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('companies.index') }}">Companies</a></li>
        <li class="breadcrumb-item active">Create</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid mb-4">
        <form action="{{ route('companies.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-lg-12">
                    @include('utils.alerts')
                    <div class="form-group">
                        <button class="btn btn-primary">Create Companies <i class="bi bi-check"></i></button>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="name" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="address">Address <span class="text-danger">*</span></label>
                                    <textarea name="address" id="address" rows="5" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active"
                                        value="is_active" {{ old('is_active') ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_active">Is Active</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('third_party_scripts')
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
@endsection

@push('page_scripts')
    <script>
        // FilePond Setup
        FilePond.registerPlugin(
            FilePondPluginImagePreview,
            FilePondPluginFileValidateSize,
            FilePondPluginFileValidateType
        );
        const fileElement = document.querySelector('input[id="image"]');
        const pond = FilePond.create(fileElement, {
            acceptedFileTypes: ['image/png', 'image/jpg', 'image/jpeg'],
        });
        FilePond.setOptions({
            server: {
                url: "{{ route('filepond.upload') }}",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                }
            }
        });

        // Company Selection Handling
        document.addEventListener("DOMContentLoaded", function() {
            const selectBox = document.getElementById("companies");
            const selectedCompaniesContainer = document.getElementById("selected-companies");
            const companiesInput = document.getElementById("companies-selected");

            selectBox.addEventListener("change", function() {
                const selectedOption = selectBox.options[selectBox.selectedIndex];

                if (selectedOption.value) {
                    const selectedDiv = document.createElement("div");
                    selectedDiv.className = "selected-company badge badge-primary p-2 m-1";
                    selectedDiv.setAttribute("data-id", selectedOption.value);
                    selectedDiv.innerHTML =
                        `${selectedOption.text} <span class="remove-company text-danger" style="cursor:pointer;">&times;</span>`;

                    // Add selected company to the hidden input
                    companiesInput.value += `${selectedOption.value},`;

                    selectedCompaniesContainer.appendChild(selectedDiv);
                    selectedOption.remove();
                }
            });

            selectedCompaniesContainer.addEventListener("click", function(event) {
                if (event.target.classList.contains("remove-company")) {
                    const companyDiv = event.target.parentElement;
                    const companyId = companyDiv.getAttribute("data-id");

                    // Remove the company from the hidden input
                    const companyIds = companiesInput.value.split(',').filter(id => id !== companyId).join(
                        ',');
                    companiesInput.value = companyIds;

                    const newOption = document.createElement("option");
                    newOption.value = companyId;
                    newOption.textContent = companyDiv.textContent.slice(0, -2);
                    selectBox.appendChild(newOption);

                    companyDiv.remove();
                }
            });
        });
    </script>
@endpush
