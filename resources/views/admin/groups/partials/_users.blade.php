
    <div class="col-lg-12">

        <div class="card">
            <div class="card-body">
                <table id="UserTable" class="table align-middle" style="width-100%">
                    <thead class="">
                        <tr class="text-uppercase table-light2">
                            <th scope="col" style="width-50px;"> ID
                            </th>
                            <th>Name</th>
                            <th>Parent Name</th>
                            <th>Role</th>

                        </tr>
                    </thead>
                    <tbody class="form-check-all">
                        @foreach ($users as $user )
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->full_name }}</td>
                            <td>{{ $user->parent->full_name }}</td>
                            <td>{{ getUserRoles($user->id) }}</td>

                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>

