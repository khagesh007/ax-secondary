<div class="col-12">
    <div class="collapse slideIn" id="collapseContactForm">
        <div class="">
            <div class=" row">
                <div>
                    <form id="mainForm" class="needs-validation" novalidate>

                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-6 row">
                                            <div class="col-4 mt-2">
                                                <label for="title" class="form-label">Name*</label>
                                            </div>
                                            <div class="col-8 mt-2">
                                                <input type="hidden" name="id" id="id" value="">
                                                <input type="text" class="form-control inputs" id="name"
                                                       name="name" placeholder="Enter name" required>
                                                <div class="invalid-feedback">
                                                    Please enter the name.
                                                </div>
                                            </div>
                                            <div class="col-4 mt-2">
                                                <label for="title" class="form-label">Description*</label>
                                            </div>
                                            <div class="col-8 mt-2">
                                                <textarea class="form-control inputs" id="description"
                                                          placeholder="Enter description" name="description" rows="6"
                                                          maxlength="2000"></textarea>
                                                <div class="invalid-feedback">
                                                    Please enter the description.
                                                </div>
                                            </div>
                                            <div class="col-4 mt-2">
                                                <label for="title" class="form-label">Frequency*</label>
                                            </div>
                                            <div class="col-8 mt-2">
                                                <select id="frequency"
                                                        class="form-select inputs form-select-transparent commonSelect2"
                                                        name="frequency" data-placeholder="Select Frequency">
                                                    <option></option>
                                                    @foreach (\App\Models\CommissionSlab::Frequencies ?? [] as $frequency)
                                                        <option value="{{ $frequency }}">{{ $frequency }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback">
                                                    Please select the frequency.
                                                </div>
                                            </div>
                                            <div class="col-4 mt-2">
                                                <label for="title" class="form-label">Commission type*</label>
                                            </div>
                                            <div class="col-8 mt-2">
                                                <select
                                                    class="form-select inputs form-select-transparent commonSelect2"
                                                    name="commission_type" id="commission_type"
                                                    data-placeholder="Select type">
                                                    <option></option>
                                                    <option value="Percentage">Percentage</option>
                                                    <option value="Fixed">Fixed</option>
                                                </select>
                                                <div class="invalid-feedback">
                                                    Please select the type.
                                                </div>
                                            </div>
                                            <div class="col-4 mt-2">
                                                <label for="value" class="form-label">Commission Value*</label>
                                            </div>
                                            <div class="col-8 mt-2">
                                                <input type="text" class="form-control inputs" id="commission_value"
                                                       name="commission_value" placeholder="Enter value" required>
                                                <div class="invalid-feedback">
                                                    Please enter the slab value.
                                                </div>
                                            </div>
                                            <div class="col-4 mt-2">
                                                <label for="title" class="form-label">Commission category*</label>
                                            </div>
                                            <div class="col-8 mt-2">
                                                <select id="commission_category"
                                                        class="form-select form-select-transparent inputs"
                                                        name="commission_category" data-placeholder="Select Category">
                                                <option></option>
                                                    <option value="Direct">Direct</option>
                                                    <option value="Indirect">Indirect</option>
                                                </select>
                                                <div class="invalid-feedback">
                                                    Please select the type.
                                                </div>
                                            </div>
                                            <div class="col-4 mt-2">
                                                <label for="title" class="form-label">Calculation From*</label>
                                            </div>
                                            <div class="col-8 mt-2">
                                                <select
                                                    class="form-select form-select-transparent inputs commonSelect2"
                                                    name="calculation_from" id="calculation_from"
                                                    data-placeholder="Select Part of amount">
                                                    <option></option>
                                                    <option value="Net Commission">Net Commission</option>
                                                    <option value="Remaining Amount">Remaining Amount</option>
                                                </select>
                                                <div class="invalid-feedback">
                                                    Please select the type.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6 row">
                                            <div class="col-4">
                                                <label for="role_id" class="form-label">Role</label>


                                            </div>
                                            <div class="col-8">
                                                <select class="form-control " name="role_id" id="role_id">

                                                    @foreach($roles as $role)
                                                        <option value="{{$role->id}}">{{$role->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-4">
                                                <label for="user_id" class="form-label">User</label>

                                            </div>
                                            <div class="col-8">
                                                <select class="form-control" name="user_id" id="user_id">

                                                </select>

                                            </div>
                                            <div class="col-4 mt-2">
                                                <label for="order" class="form-label"> Order</label>
                                            </div>
                                            <div class="col-8 mt-2">
                                                <input type="number" class="form-control inputs" id="order"
                                                       name="order" placeholder="Enter order">
                                                <div class="invalid-feedback">
                                                    Please enter the order.
                                                </div>
                                            </div>
                                            <div class="col-4 mt-2">
                                                <label for="title" class="form-label">Icon</label>
                                            </div>
                                            <div class="col-8 mt-2">
                                                <input type="text" class="form-control inputs" id="icon"
                                                       name="icon" placeholder="ri line">
                                                <div class="invalid-feedback">
                                                    Please enter the icon class.
                                                </div>
                                            </div>
                                            <!--end col-->
                                            <div class="col-4 mt-2">
                                                <label for="badge" class="form-label"> Badge</label>
                                            </div>
                                            <div class="col-8 mt-2">
                                                <select id="statusBadge"
                                                        class="form-select form-select-transparent commonSelect2 inputs"
                                                        name="status_badge" data-placeholder="Select Badge">
                                                    <option></option>
                                                    @include('admin.partials.badgeoptions')
                                                </select>
                                                <div class="invalid-feedback">
                                                    Please enter the status Badge.
                                                </div>
                                            </div>
                                            <div class="col-4 mt-2">
                                                <label for="thumbnail" class="form-label">Thumbnail</label>
                                            </div>
                                            <div class="col-8 mt-2">
                                                <div class="mb-2  d-none" id="thumbnaildiv">
                                                    <img src="" alt="Image" id="thumb-img"
                                                         class="img-thumbnail img-responsive" style="max-width:50px">
                                                    <i class="ri-delete-bin-5-fill text-danger ms-2 generalDelete"
                                                       data-id="" id="thumbnail_id" data-key=""
                                                       data-model="App\Models\CommissionSlab"></i>
                                                </div>
                                                <input type="file" class="form-control" id="thumbnail"
                                                       name="thumbnail">
                                                <div class="invalid-feedback">
                                                    Please enter the Thumbnail.
                                                </div>
                                            </div>
                                            <div class="col-4 mt-2">
                                                <label for="thumbnail" class="form-label">Image</label>
                                            </div>
                                            <div class="col-8 mt-2">
                                                <div class="mb-2  d-none" id="imagediv">
                                                    <img src="" alt="Image" id="image-img"
                                                         class="img-thumbnail img-responsive" style="max-width:50px">
                                                    <i class="ri-delete-bin-5-fill text-danger ms-2 generalDelete"
                                                       data-id="" data-model="App\Models\Source" data-key=""
                                                       id="image_id"></i>
                                                </div>
                                                <input type="file" class="form-control" id="image"
                                                       name="image">
                                                <div class="invalid-feedback">
                                                    Please enter the Status image.
                                                </div>
                                            </div>
                                            <!--end col-->
                                            <!--end col-->
                                            <div class="col-4 mt-2">
                                                <label class="form-check-label" for="statusCheckbox">Status</label>
                                            </div>
                                            <div class="col-8 mt-2">
                                                <div class="form-check form-switch form-switch-lg form-switch-success"
                                                     dir="ltr">
                                                    <input type="checkbox" name="status" class="form-check-input"
                                                           id="statusCheckbox" checked>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row" id="appendLimits">
                                        <div class="col-md-12">
                                            <div id="tableContainerDiv" class="mt-3">
                                                <hr>
                                                <div class=" mb-2">

                                                    <div class="">
                                                        <div class="">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <h4> Slab Limits </h4>
                                                                </div>
                                                            </div>
                                                            <div id="tableContainer">
                                                                <table class="table table-nowrap" id="myTable">
                                                                    <thead>
                                                                    <tr>
                                                                        <th>ID</th>
                                                                        <th>Range</th>
                                                                        <th>Type</th>
                                                                        <th>Value</th>
                                                                        <th>Actions</th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody id="slabLimitBody">

                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end col-->
                        <button type="submit" class="d-none" id="submitBtn"></button>

                        <!--end col-->
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
