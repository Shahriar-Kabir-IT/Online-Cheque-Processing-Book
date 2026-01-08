<?php
$pageTitle = 'Reports';
require_once 'header.php';
?>
<div class="page-content">
    <h2>Generate Reports</h2>
    
    <form method="POST" action="operation_cheque.php" class="report-form">
        <div class="form-row">
            <div class="form-group">
                <label for="report_type">Report Type *</label>
                <select id="report_type" name="report_type" required>
                    <option value="1">PDF Report</option>
                    <option value="2">Excel Report</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="">All</option>
                    <option value="1">Printed</option>
                    <option value="2">Pending</option>
                    <option value="3">Cancelled</option>
                </select>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="from_date">From Date</label>
                <input type="date" id="from_date" name="from_date">
            </div>
            
            <div class="form-group">
                <label for="to_date">To Date</label>
                <input type="date" id="to_date" name="to_date">
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" name="BtnPublishReport" class="btn btn-primary">Generate Report</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
// Set default date range (last 30 days)
const today = new Date();
const lastMonth = new Date();
lastMonth.setDate(lastMonth.getDate() - 30);

document.getElementById('to_date').valueAsDate = today;
document.getElementById('from_date').valueAsDate = lastMonth;
</script>

<?php require_once 'footer.php'; ?>
