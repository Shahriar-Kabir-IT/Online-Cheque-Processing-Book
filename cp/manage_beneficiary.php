<?php
$pageTitle = 'Manage Beneficiaries';
require_once 'header.php';
require_once '../DataAccess.php';
require_once '../class/beneficiary/BeneficiaryModel.php';

$dao = new DataAccess();
$Beneficiary = new BeneficiaryModel($dao);

$beneficiaries = $Beneficiary->ListAll('', [], 'ob_name', 'ASC');
?>
<div class="page-content">
    <h2>Manage Beneficiaries</h2>
    
    <div class="page-actions">
        <button onclick="showAddForm()" class="btn btn-primary">Add Beneficiary</button>
    </div>
    
    <div id="addForm" style="display: none; margin-bottom: 20px;">
        <form method="POST" action="operation_beneficiary.php" class="inline-form">
            <input type="text" name="ob_name" placeholder="Beneficiary Name" required>
            <button type="submit" name="BtnSaveBeneficiary" class="btn btn-primary">Save</button>
            <button type="button" onclick="hideAddForm()" class="btn btn-secondary">Cancel</button>
        </form>
    </div>
    
    <?php if (empty($beneficiaries)): ?>
        <p class="no-data">No beneficiaries found.</p>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Beneficiary Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($beneficiaries as $ben): ?>
                    <tr>
                        <td><?php echo $ben['ob_id']; ?></td>
                        <td><?php echo htmlspecialchars($ben['ob_name']); ?></td>
                        <td>
                            <a href="edit_beneficiary.php?ob_id=<?php echo $ben['ob_id']; ?>" class="btn btn-sm btn-secondary">Edit</a>
                            <a href="operation_beneficiary.php?action=delete&ob_id=<?php echo $ben['ob_id']; ?>" 
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('Are you sure you want to delete this beneficiary?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script>
function showAddForm() {
    document.getElementById('addForm').style.display = 'block';
}

function hideAddForm() {
    document.getElementById('addForm').style.display = 'none';
}
</script>

<?php require_once 'footer.php'; ?>
