var OrdersService = {
    getOrdersReport: function(callback, error_callback) {
        console.log('OrdersService: Calling orders report API...');
        var url = "backend/rest/orders/report";
        console.log('OrdersService: Full URL will be:', Constants.get_api_base_url() + url);
        RestClient.get(url, callback, error_callback);
    },
    
    getOrderDetails: function(order_id, callback, error_callback) {
        console.log('OrdersService: Calling order details API for order:', order_id);
        RestClient.get("backend/rest/order/details/" + order_id, callback, error_callback);
    },

    // Initialize orders page functionality
    init: function() {
        $(document).ready(function() {
            // Handle SPA page changes
            $(document).on('spapp.page.loaded', function(event, data) {
                console.log('Page loaded:', data);
                if (data.name === 'orders') {
                    console.log('Orders page loaded, initializing...');
                    OrdersService.loadOrdersTable();
                }
            });
            
            // Also try to load immediately if already on orders page
            if (window.location.hash === '#orders') {
                console.log('Already on orders page, loading...');
                setTimeout(function() {
                    OrdersService.loadOrdersTable();
                }, 500);
            }
        });
    },

    loadOrdersTable: function() {
        console.log('Loading orders table...');
        OrdersService.getOrdersReport(
            function(data) {
                console.log('Orders data received:', data);
                
                // Clear existing table body
                $('#order-details tbody').empty();
                
                // Check if data is valid
                if (!data || !Array.isArray(data)) {
                    console.error('Invalid data format received:', data);
                    alert('Invalid data format received from server');
                    return;
                }
                
                // Populate table with data
                data.forEach(function(order) {
                    $('#order-details tbody').append(`
                        <tr>
                            <td class="text-center">${order.details}</td>
                            <td>${order.order_number}</td>
                            <td>${order.total_amount}</td>
                        </tr>
                    `);
                });
                
                // Initialize DataTable with pagination, search, and sorting
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
                console.error('Error loading orders:', error);
                console.error('Error details:', error.responseText);
                alert('Failed to load orders data. Check console for details.');
            }
        );
    },

    showOrderDetails: function(orderId) {
        OrdersService.getOrderDetails(orderId,
            function(data) {
                // Clear existing table body in modal
                $('#order-details-modal tbody').empty();
                
                let totalBill = 0;
                
                // Populate modal table with order details
                data.forEach(function(item, index) {
                    const itemTotal = item.quantity * parseFloat(item.price_each);
                    totalBill += itemTotal;
                    
                    $('#order-details-modal tbody').append(`
                        <tr>
                            <th scope="row">${index + 1}</th>
                            <td>${item.product_name}</td>
                            <td>${item.quantity}</td>
                            <td>${parseFloat(item.price_each).toFixed(2)}</td>
                        </tr>
                    `);
                });
                
                // Add total bill row
                $('#order-details-modal tbody').append(`
                    <tr>
                        <td colspan="3"><strong>Total bill</strong></td>
                        <td><strong>${totalBill.toFixed(2)}</strong></td>
                    </tr>
                `);
            },
            function(error) {
                console.error('Error loading order details:', error);
                alert('Failed to load order details');
            }
        );
    }
}

// Initialize the orders functionality
OrdersService.init();

// Make showOrderDetails globally available for HTML onclick events
window.showOrderDetails = OrdersService.showOrderDetails;
