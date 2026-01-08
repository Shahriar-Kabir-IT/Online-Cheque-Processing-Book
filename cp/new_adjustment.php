<?php
$pageTitle = 'New Adjustment';
require_once 'header.php';
require_once '../DataAccess.php';
require_once '../class/company/CompanyModel.php';
require_once '../class/bank/BankModel.php';
require_once '../class/account/AccountModel.php';

$dao = new DataAccess();
$Company = new CompanyModel($dao);
$Bank = new BankModel($dao);
$Account = new AccountModel($dao);

$companies = $Company->ListAll('', [], 'oc_name', 'ASC');
$banks = $Bank->ListAll('', [], 'ocq_bank', 'ASC');
$accounts = $Account->ListAllAccount('', [], 'a.acc_number', 'ASC');
?>
<div class="page-content">
    <h2>Create New Adjustment</h2>
    
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">Adjustment created successfully!</div>
    <?php endif; ?>
    
    <form method="POST" action="operation_cheque.php" class="adjustment-form">
        <div class="form-row">
            <div class="form-group">
                <label for="adjustment_company">Company *</label>
                <select id="adjustment_company" name="adjustment_company" required>
                    <option value="">Select Company</option>
                    <?php foreach ($companies as $comp): ?>
                        <option value="<?php echo $comp['oc_id']; ?>"><?php echo htmlspecialchars($comp['oc_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="adjustment_bank">Bank *</label>
                <select id="adjustment_bank" name="adjustment_bank" required>
                    <option value="">Select Bank</option>
                    <?php foreach ($banks as $bank): ?>
                        <option value="<?php echo $bank['ocq_id']; ?>"><?php echo htmlspecialchars($bank['ocq_bank']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="adjustment_account">Account Number *</label>
                <select id="adjustment_account" name="adjustment_account" required>
                    <option value="">Select Account</option>
                    <?php foreach ($accounts as $acc): ?>
                        <option value="<?php echo htmlspecialchars($acc['acc_number']); ?>">
                            <?php echo htmlspecialchars($acc['acc_number'] . ' - ' . ($acc['company_name'] ?? '')); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="adjustment_type">Adjustment Type *</label>
                <select id="adjustment_type" name="adjustment_type" required>
                    <option value="Positive">Positive</option>
                    <option value="Negative">Negative</option>
                </select>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="adjustment_amount">Amount *</label>
                <input type="number" id="adjustment_amount" name="adjustment_amount" step="0.01" min="0" required>
            </div>
            
            <div class="form-group">
                <label for="adjustment_date">Date *</label>
                <input type="date" id="adjustment_date" name="adjustment_date" required>
            </div>
        </div>
        
        <div class="form-group">
            <label for="adjustment_reason">Reason *</label>
            <textarea id="adjustment_reason" name="adjustment_reason" rows="3" required></textarea>
        </div>
        
        <div class="form-actions">
            <button type="submit" name="BtnSaveAdjustment" class="btn btn-primary">Save Adjustment</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
// Set today's date as default
document.getElementById('adjustment_date').valueAsDate = new Date();
</script>

<?php require_once 'footer.php'; ?>
