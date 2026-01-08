<?php
$pageTitle = 'Edit Beneficiary';
require_once 'header.php';
require_once '../DataAccess.php';
require_once '../class/beneficiary/BeneficiaryModel.php';
require_once '../class/common/Validation.php';

$dao = new DataAccess();
$ob_id = Validation::getGet('ob_id', 0, 'int');

if ($ob_id <= 0) {
    header('Location: manage_beneficiary.php');
    exit();
}

$Beneficiary = new BeneficiaryModel($dao);
$beneficiaries = $Beneficiary->ListAll("ob_id = ?", [$ob_id]);

if (empty($beneficiaries)) {
    header('Location: manage_beneficiary.php');
    exit();
}

$ben = $beneficiaries[0];
?>
<div class="page-content">
    <h2>Edit Beneficiary</h2>
    
    <form method="POST" action="operation_beneficiary.php" class="beneficiary-form">
        <input type="hidden" name="ob_id" value="<?php echo $ben['ob_id']; ?>">
        
        <div class="form-group">
            <label for="ob_name">Beneficiary Name *</label>
            <input type="text" id="ob_name" name="ob_name" value="<?php echo htmlspecialchars($ben['ob_name']); ?>" required>
        </div>
        
        <div class="form-actions">
            <button type="submit" name="BtnUpdateBeneficiary" class="btn btn-primary">Update</button>
            <a href="manage_beneficiary.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
<?php require_once 'footer.php'; ?>
