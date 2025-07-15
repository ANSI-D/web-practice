var EmployeesService = {
    delete_employee: function(employee_id) {
        if (
          confirm(
            "Do you want to delete employee with the id " + employee_id + "?"
          ) == true
        ) {
          console.log("TODO Perform deletion logic");
        }
    },
    edit_employee: function(employee_id){
        // Fetch employee data from backend (correct endpoint)
        $.getJSON('/web-programming-final/backend/rest/employee/' + employee_id, function(emp) {
            // Populate modal fields
            $('#employeeNumber').val(emp.employeeNumber);
            $('#firstName').val(emp.firstName);
            $('#lastName').val(emp.lastName);
            $('#email').val(emp.email);
            // Show modal
            $('#edit-employee-modal').modal('show');
        });
    },
    get_all_employees: function() {
        return $.getJSON('/web-programming-final/backend/rest/employees/performance');
    },
    populate_employee_table: function() {
        EmployeesService.get_all_employees().done(function(data) {
            var tbody = '';
            data.forEach(function(emp) {
                tbody += `<tr>
                    <td class="text-center">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-warning" onclick="EmployeesService.edit_employee(${emp.id})">Edit</button>
                            <button type="button" class="btn btn-danger" onclick="EmployeesService.delete_employee(${emp.id})">Delete</button>
                        </div>
                    </td>
                    <td>${emp.full_name}</td>
                    <td>${emp.email}</td>
                    <td>${emp.total}</td>
                </tr>`;
            });
            $('#employee-performance tbody').html(tbody);
        });
    },
    init: function() {
        EmployeesService.populate_employee_table();
        // Attach submit handler for the edit employee modal form
        $(document).on('submit', '#edit-employee-modal form', function(e) {
            e.preventDefault(); // Prevent default form submission and page redirect
            var employee_id = $('#employeeNumber').val();
            var data = {
                first_name: $('#firstName').val(),
                last_name: $('#lastName').val(),
                email: $('#email').val()
            };
            $.ajax({
                url: '/web-programming-final/backend/rest/employee/edit/' + employee_id,
                type: 'PUT',
                contentType: 'application/json',
                data: JSON.stringify(data),
                success: function(response) {
                    // Hide modal, refresh table
                    $('#edit-employee-modal').modal('hide');
                    EmployeesService.populate_employee_table();
                },
                error: function(xhr) {
                    alert('Failed to save changes.');
                }
            });
        });
    }
}

$(document).ready(function() {
    EmployeesService.init();
});