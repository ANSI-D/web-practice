var CustomersService = {
    getAll: function(callback, error_callback) {
        RestClient.get('/customers', callback, error_callback);
    },
    getMeals: function(customerId, callback, error_callback) {
        RestClient.get('/customer/meals/' + customerId, callback, error_callback);
    },
    
    add: function(customerData, callback, error_callback) {
        RestClient.post('/customers/add', customerData, callback, error_callback);
    }
}

/* 
  1. Populate the <select> HTML element with the id `customers-list` with all 
     customers from the database (15 points)
  2. When the select list item changes (other customer is selected) fetch all 
     meals for the selected customer and populate the table with the id `customer-meals`
     with the meals you get from the database (15 points)
  3. Use the modal with id `add-customer-modal` to add customer to the database. (15 points)
  3.1. After the customer has been added successfully, refresh the list of customers
       in the select list with the id `customers-list` (5 points)
 */
$(document).ready(function() {
  loadCustomers();
  
  function loadCustomers() {
  CustomersService.getAll(function(customers) {
    const customersList = $('#customers-list');
    // Clear existing options except the first one
    //customersList.find('option:not(:first)').remove();
    
    // Add each customer as an option

    customers.forEach(function(customer) {
      const option = `<option value="${customer.id}">${customer.first_name} ${customer.last_name}</option>`;
      customersList.append(option);
    });
  }, function(error) {
    console.error('Error loading customers:', error);
  });
}
  // Handle customer selection change
  $('#customers-list').on('change', function() {
    const customerId = $(this).val();
    if (customerId && customerId !== 'Please select one customer') {
      loadCustomerMeals(customerId);
    } else {
      // Clear meals table if no customer selected
      $('#customer-meals tbody').empty();
    }
  });
});

function loadCustomerMeals(customerId) {
  CustomersService.getMeals(customerId, function(meals) {
    const tbody = $('#customer-meals tbody');
    tbody.empty();
    
    if (meals && meals.length > 0) {
      meals.forEach(function(meal) {
        const row = `
          <tr>
            <td>${meal.food_name}</td>
            <td>${meal.food_brand}</td>
            <td>${meal.meal_date}</td>
          </tr>
        `;
        tbody.append(row);
      });
    } else {
      tbody.append('<tr><td colspan="3" class="text-center">No meals found for this customer.</td></tr>');
    }
  }, function(error) {
    console.error('Error loading customer meals:', error);
    const tbody = $('#customer-meals tbody');
    tbody.empty();
    tbody.append('<tr><td colspan="3" class="text-center text-danger">Error loading meals. Please try again.</td></tr>');
  });
}

// ========================================================================
// TASK 3 & 3.1: Add Customer Modal Form Submission (15 + 5 points)
// ========================================================================

// Handle form submission for adding new customer
$('#add-customer-form').on('submit', function(e) {
  e.preventDefault();
  
  const customerData = {
    first_name: $('#first_name').val(),
    last_name: $('#last_name').val(),
    email: $('#email').val()
  };
  
  CustomersService.add(customerData, function(response) {
    // Close the modal
    $('#add-customer-modal').modal('hide');
    
    // Clear the form
    $('#add-customer-form')[0].reset();
    
    // Refresh the customers list (Task 3.1)
    loadCustomers();
  }, function(error) {
    console.error('Error adding customer:', error);
  });
});