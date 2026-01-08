<?php
$pageTitle = 'New Account';
require_once 'header.php';
require_once '../DataAccess.php';
require_once '../class/company/CompanyModel.php';
require_once '../class/bank/BankModel.php';
require_once '../class/account/AccountModel.php';

$dao = new DataAccess();
$Company = new CompanyModel($dao);
$Bank = new BankModel($dao);

$companies = $Company->ListAll('', [], 'oc_name', 'ASC');
$banks = $Bank->ListAll('', [], 'ocq_bank', 'ASC');
?>
<div class="page-content">
    <h2>Create New Account</h2>
    
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">Account created successfully!</div>
    <?php endif; ?>
    
    <form method="POST" action="operation_account.php" class="account-form">
        <div class="form-row">
            <div class="form-group">
                <label for="ocq_company">Company *</label>
                <select id="ocq_company" name="ocq_company" required>
                    <option value="">Select Company</option>
                    <?php foreach ($companies as $comp): ?>
                        <option value="<?php echo $comp['oc_id']; ?>"><?php echo htmlspecialchars($comp['oc_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="bank">Bank *</label>
                <select id="bank" name="bank" required>
                    <option value="">Select Bank</option>
                    <?php foreach ($banks as $bank): ?>
                        <option value="<?php echo $bank['ocq_id']; ?>"><?php echo htmlspecialchars($bank['ocq_bank']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="acc_number">Account Number *</label>
                <input type="text" id="acc_number" name="acc_number" required>
            </div>
            
            <div class="form-group">
                <label for="ac_code">Account Code</label>
                <input type="text" id="ac_code" name="ac_code">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="branch">Branch</label>
                <input type="text" id="branch" name="branch">
            </div>
            
            <div class="form-group">
                <label for="ac_type">Account Type</label>
                <input type="text" id="ac_type" name="ac_type">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="chequebook">Chequebook Number</label>
                <input type="text" id="chequebook" name="chequebook">
            </div>
            
            <div class="form-group">
                <label for="leafs">Leaves</label>
                <input type="number" id="leafs" name="leafs" min="0">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="balance">Initial Balance</label>
                <input type="number" id="balance" name="balance" step="0.01" value="0">
            </div>
            
            <div class="form-group">
                <label for="chqbookdate">Chequebook Date</label>
                <input type="date" id="chqbookdate" name="chqbookdate">
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" name="BtnSaveAccount" class="btn btn-primary">Save Account</button>
            <a href="bankaccount.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
<?php require_once 'footer.php'; ?>
