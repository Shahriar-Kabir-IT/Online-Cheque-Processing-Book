<?php
$pageTitle = 'Edit Cheque';
require_once 'header.php';
require_once '../DataAccess.php';
require_once '../class/cheque/ChequeModel.php';
require_once '../class/company/CompanyModel.php';
require_once '../class/bank/BankModel.php';
require_once '../class/beneficiary/BeneficiaryModel.php';
require_once '../class/account/AccountModel.php';
require_once '../class/signatory/SignatoryModel.php';
require_once '../class/common/Validation.php';
require_once '../class/common/functions.php';

$dao = new DataAccess();
$ocq_id = Validation::getGet('ocq_id', 0, 'int');

if ($ocq_id <= 0) {
    header('Location: pending_cheques.php');
    exit();
}

$Cheque = new ChequeModel($dao);
$cheques = $Cheque->ListAllCHQ("c.ocq_id = ?", [$ocq_id]);

if (empty($cheques)) {
    header('Location: pending_cheques.php');
    exit();
}

$chq = $cheques[0];

$Company = new CompanyModel($dao);
$Bank = new BankModel($dao);
$Beneficiary = new BeneficiaryModel($dao);
$Account = new AccountModel($dao);
$Signatory = new SignatoryModel($dao);

$companies = $Company->ListAll('', [], 'oc_name', 'ASC');
$banks = $Bank->ListAll('', [], 'ocq_bank', 'ASC');
$beneficiaries = $Beneficiary->ListAll('', [], 'ob_name', 'ASC');
$signatories = $Signatory->ListAll('', [], 'ocq_signatory', 'ASC');
$accounts = $Account->ListAllAccount("a.ocq_company = ?", [$chq['ocq_company']]);
?>
<div class="page-content">
    <h2>Edit Cheque</h2>
    
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">Cheque updated successfully!</div>
    <?php endif; ?>
    
    <form method="POST" action="operation_cheque.php" class="cheque-form">
        <input type="hidden" name="ocq_id" value="<?php echo $chq['ocq_id']; ?>">
        
        <div class="form-row">
            <div class="form-group">
                <label for="ocq_company">Company *</label>
                <select id="ocq_company" name="ocq_company" required>
                    <?php foreach ($companies as $comp): ?>
                        <option value="<?php echo $comp['oc_id']; ?>" <?php echo $chq['ocq_company'] == $comp['oc_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($comp['oc_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="ocq_bank">Bank *</label>
                <select id="ocq_bank" name="ocq_bank" required>
                    <?php foreach ($banks as $bank): ?>
                        <option value="<?php echo $bank['ocq_id']; ?>" <?php echo $chq['ocq_bank'] == $bank['ocq_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($bank['ocq_bank']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="ocq_accno">Account Number *</label>
                <input type="text" id="ocq_accno" name="ocq_accno" value="<?php echo htmlspecialchars($chq['ocq_accno']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="ocq_chqno">Cheque Number *</label>
                <input type="text" id="ocq_chqno" name="ocq_chqno" value="<?php echo htmlspecialchars($chq['ocq_chqno']); ?>" required>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="ocq_beneficiary">Beneficiary *</label>
                <input type="text" id="ocq_beneficiary" name="ocq_beneficiary" value="<?php echo htmlspecialchars($chq['ocq_beneficiary']); ?>" list="beneficiary-list" required>
                <datalist id="beneficiary-list">
                    <?php foreach ($beneficiaries as $ben): ?>
                        <option value="<?php echo htmlspecialchars($ben['ob_name']); ?>">
                    <?php endforeach; ?>
                </datalist>
            </div>
            
            <div class="form-group">
                <label for="ocq_signatory">Signatory *</label>
                <select id="ocq_signatory" name="ocq_signatory" required>
                    <?php foreach ($signatories as $sig): ?>
                        <option value="<?php echo $sig['ocq_id']; ?>" <?php echo $chq['ocq_signatory'] == $sig['ocq_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($sig['ocq_signatory'] . ' - ' . $sig['ocq_designation']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="ocq_amount">Amount *</label>
                <input type="number" id="ocq_amount" name="ocq_amount" step="0.01" min="0" value="<?php echo $chq['ocq_amount']; ?>" required>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="ocq_date">Date *</label>
                    <input type="date" id="ocq_date" name="ocq_date" value="<?php echo $chq['ocq_date']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="ocq_status">Status *</label>
                    <select id="ocq_status" name="ocq_status" required>
                        <option value="1" <?php echo $chq['ocq_status'] == 1 ? 'selected' : ''; ?>>Printed</option>
                        <option value="2" <?php echo $chq['ocq_status'] == 2 ? 'selected' : ''; ?>>Pending</option>
                        <option value="3" <?php echo $chq['ocq_status'] == 3 ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <label for="ocq_purpose">Purpose</label>
            <textarea id="ocq_purpose" name="ocq_purpose" rows="3"><?php echo htmlspecialchars($chq['ocq_purpose'] ?? ''); ?></textarea>
        </div>
        
        <div class="form-actions">
            <button type="submit" name="BtnUpdateCheque" class="btn btn-primary">Update Cheque</button>
            <a href="pending_cheques.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
<?php require_once 'footer.php'; ?>
