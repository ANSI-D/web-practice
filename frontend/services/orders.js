// OrdersService: Handles all order-related frontend logic
var OrdersService = {
    // Fetches the orders report from the backend API
    getOrdersReport: function(callback, error_callback) {
        // Log the API call for debugging
        console.log('OrdersService: Calling orders report API...');
        var url = "backend/rest/orders/report";
        // Log the full URL (with base) for debugging
        console.log('OrdersService: Full URL will be:', Constants.get_api_base_url() + url);
        // Use RestClient utility to make the GET request
        RestClient.get(url, callback, error_callback);
    },
    
    // Fetches the details for a specific order by order_id
    getOrderDetails: function(order_id, callback, error_callback) {
        console.log('OrdersService: Calling order details API for order:', order_id);
        // Use RestClient utility to make the GET request for order details
        RestClient.get("backend/rest/order/details/" + order_id, callback, error_callback);
    },

    // Initializes the orders page functionality (SPA and table loading)
    init: function() {
        $(document).ready(function() {
            // Listen for SPA page load events
            $(document).on('spapp.page.loaded', function(event, data) {
                console.log('Page loaded:', data);
                // If the loaded page is 'orders', load the orders table
                if (data.name === 'orders') {
                    console.log('Orders page loaded, initializing...');
                    OrdersService.loadOrdersTable();
                }
            });
            
            // If already on the orders page (e.g., on refresh), load the table after a short delay
            if (window.location.hash === '#orders') {
                console.log('Already on orders page, loading...');
                setTimeout(function() {
                    OrdersService.loadOrdersTable();
                }, 500);
            }
        });
    },

    // Loads the orders table with data from the backend
    loadOrdersTable: function() {
        console.log('Loading orders table...');
        // Fetch the orders report
        OrdersService.getOrdersReport(
            function(data) {
                console.log('Orders data received:', data);
                
                // Clear any existing rows in the table body
                $('#order-details tbody').empty();
                
                // Validate the data format
                if (!data || !Array.isArray(data)) {
                    console.error('Invalid data format received:', data);
                    alert('Invalid data format received from server');
                    return;
                }
                
                // Populate the table with each order's data
                data.forEach(function(order) {
                    $('#order-details tbody').append(`
                        <tr>
                            <td class="text-center">${order.details}</td>
                            <td>${order.order_number}</td>
                            <td>${order.total_amount}</td>
                        </tr>
                    `);
                });
                
                // Initialize DataTable plugin for pagination, search, and sorting
                $('#order-details').DataTable({
                    "pageLength": 10,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "lengthChange": true
                });
                
                console.log('DataTable initialized successfully');
            },
            function(error) {
                // Handle errors from the API call
                console.error('Error loading orders:', error);
                console.error('Error details:', error.responseText);
                alert('Failed to load orders data. Check console for details.');
            }
        );
    },

    // Shows the details for a specific order in a modal
    showOrderDetails: function(orderId) {
        // Fetch order details from the backend
        OrdersService.getOrderDetails(orderId,
            function(data) {
                // Clear any existing rows in the modal's table body
                $('#order-details-modal tbody').empty();
                
                let totalBill = 0; // Track the total bill for the order
                
                // Populate the modal table with each product in the order
                data.forEach(function(item, index) {
                    // Calculate the total for this line item
                    const itemTotal = item.quantity * parseFloat(item.price_each);
                    totalBill += itemTotal;
                    
                    // Add a row for this product
                    $('#order-details-modal tbody').append(`
                        <tr>
                            <th scope="row">${index + 1}</th>
                            <td>${item.product_name}</td>
                            <td>${item.quantity}</td>
                            <td>${parseFloat(item.price_each).toFixed(2)}</td>
                        </tr>
                    `);
                });
                
                // Add a final row showing the total bill for the order
                $('#order-details-modal tbody').append(`
                    <tr>
                        <td colspan="3"><strong>Total bill</strong></td>
                        <td><strong>${totalBill.toFixed(2)}</strong></td>
                    </tr>
                `);
            },
            function(error) {
                // Handle errors from the API call
                console.error('Error loading order details:', error);
                alert('Failed to load order details');
            }
        );
    }
}

// Initialize the orders functionality when the script loads
OrdersService.init();

// Make showOrderDetails globally available for HTML onclick events
window.showOrderDetails = OrdersService.showOrderDetails;
