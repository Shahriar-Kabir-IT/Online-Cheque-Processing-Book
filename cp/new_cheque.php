<?php
$pageTitle = 'New Cheque';
require_once 'header.php';
require_once '../DataAccess.php';
require_once '../class/company/CompanyModel.php';
require_once '../class/bank/BankModel.php';
require_once '../class/beneficiary/BeneficiaryModel.php';
require_once '../class/account/AccountModel.php';
require_once '../class/signatory/SignatoryModel.php';
require_once '../class/common/functions.php';

$dao = new DataAccess();
$Company = new CompanyModel($dao);
$Bank = new BankModel($dao);
$Beneficiary = new BeneficiaryModel($dao);
$Account = new AccountModel($dao);
$Signatory = new SignatoryModel($dao);

$companies = $Company->ListAll('', [], 'oc_name', 'ASC');
$banks = $Bank->ListAll('', [], 'ocq_bank', 'ASC');
$beneficiaries = $Beneficiary->ListAll('', [], 'ob_name', 'ASC');
$signatories = $Signatory->ListAll('', [], 'ocq_signatory', 'ASC');

$selectedCompany = isset($_GET['oc_id']) ? (int)$_GET['oc_id'] : 0;
$accounts = [];
if ($selectedCompany > 0) {
    $accounts = $Account->ListAllAccount("a.ocq_company = ?", [$selectedCompany]);
}
?>
<div class="page-content">
    <h2>Create New Cheque</h2>
    
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">Cheque created successfully!</div>
    <?php endif; ?>
    
    <form method="POST" action="operation_cheque.php" class="cheque-form">
        <div class="form-row">
            <div class="form-group">
                <label for="ocq_company">Company *</label>
                <select id="ocq_company" name="ocq_company" required onchange="loadAccounts(this.value)">
                    <option value="">Select Company</option>
                    <?php foreach ($companies as $comp): ?>
                        <option value="<?php echo $comp['oc_id']; ?>" <?php echo $selectedCompany == $comp['oc_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($comp['oc_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="ocq_bank">Bank *</label>
                <select id="ocq_bank" name="ocq_bank" required onchange="loadAccountsByBank()">
                    <option value="">Select Bank</option>
                    <?php foreach ($banks as $bank): ?>
                        <option value="<?php echo $bank['ocq_id']; ?>"><?php echo htmlspecialchars($bank['ocq_bank']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="ocq_accno">Account Number *</label>
                <select id="ocq_accno" name="ocq_accno" required>
                    <option value="">Select Account</option>
                    <?php foreach ($accounts as $acc): ?>
                        <option value="<?php echo htmlspecialchars($acc['acc_number']); ?>" data-bank="<?php echo $acc['bank']; ?>">
                            <?php echo htmlspecialchars($acc['acc_number'] . ' - ' . ($acc['bank_name'] ?? '')); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="ocq_chqno">Cheque Number *</label>
                <input type="text" id="ocq_chqno" name="ocq_chqno" required>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="ocq_onbehalf">On Behalf Of</label>
                <input type="text" id="ocq_onbehalf" name="ocq_onbehalf">
            </div>
            
            <div class="form-group">
                <label for="ocq_type">Cheque Type *</label>
                <select id="ocq_type" name="ocq_type" required>
                    <option value="1">Company</option>
                    <option value="2">Personal</option>
                </select>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="ocq_beneficiary">Beneficiary *</label>
                <input type="text" id="ocq_beneficiary" name="ocq_beneficiary" list="beneficiary-list" required>
                <datalist id="beneficiary-list">
                    <?php foreach ($beneficiaries as $ben): ?>
                        <option value="<?php echo htmlspecialchars($ben['ob_name']); ?>">
                    <?php endforeach; ?>
                </datalist>
            </div>
            
            <div class="form-group">
                <label for="ocq_signatory">Signatory *</label>
                <select id="ocq_signatory" name="ocq_signatory" required>
                    <option value="">Select Signatory</option>
                    <?php foreach ($signatories as $sig): ?>
                        <option value="<?php echo $sig['ocq_id']; ?>">
                            <?php echo htmlspecialchars($sig['ocq_signatory'] . ' - ' . $sig['ocq_designation']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="ocq_amount">Amount *</label>
                <input type="number" id="ocq_amount" name="ocq_amount" step="0.01" min="0" required>
            </div>
            
            <div class="form-group">
                <label for="ocq_date">Date *</label>
                <input type="date" id="ocq_date" name="ocq_date" required>
            </div>
        </div>
        
        <div class="form-group">
            <label for="ocq_purpose">Purpose</label>
            <textarea id="ocq_purpose" name="ocq_purpose" rows="3"></textarea>
        </div>
        
        <div class="form-actions">
            <button type="submit" name="BtnSaveCheque" class="btn btn-primary">Save Cheque</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
function loadAccounts(companyId) {
    if (!companyId) {
        document.getElementById('ocq_accno').innerHTML = '<option value="">Select Account</option>';
        return;
    }
    // Reload page with company ID
    window.location.href = 'new_cheque.php?oc_id=' + companyId;
}

function loadAccountsByBank() {
    const bankId = document.getElementById('ocq_bank').value;
    const accountSelect = document.getElementById('ocq_accno');
    const options = accountSelect.querySelectorAll('option');
    
    options.forEach(opt => {
        if (opt.value === '') return;
        const optBank = opt.getAttribute('data-bank');
        if (optBank == bankId) {
            opt.style.display = '';
        } else {
            opt.style.display = 'none';
        }
    });
}

// Set today's date as default
document.getElementById('ocq_date').valueAsDate = new Date();
</script>

<?php require_once 'footer.php'; ?>
