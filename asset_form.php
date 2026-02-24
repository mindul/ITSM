<?php
require_once 'config/db.php';
require_once 'includes/functions.php';
include 'includes/header.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$asset = [
    'model_name' => '',
    'serial_number' => '',
    'category_id' => '',
    'ip_address' => '',
    'vlan_info' => '',
    'location' => '',
    'status' => '재고',
    'importance' => 'Medium',
    'risk_level' => 'Medium',
    'manager_name' => '',
    'introduction_date' => date('Y-m-d')
];

if ($id > 0) {
    $stmt = $pdo->prepare("SELECT a.*, c.name as category_name FROM assets a JOIN categories c ON a.category_id = c.id WHERE a.id = ?");
    $stmt->execute([$id]);
    $asset = $stmt->fetch();
    if (!$asset)
        die("자산을 찾을 수 없습니다.");

    // Permission check for editing
    if (!canEditAsset($asset['category_name'])) {
        die("이 카테고리의 자산을 수정할 권한이 없습니다.");
    }
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
?>

<div class="pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <?php echo $id > 0 ? '자산 수정' : '신규 자산 등록'; ?>
    </h1>
</div>

<div class="card col-md-8">
    <div class="card-body">
        <form action="asset_actions.php" method="POST">
            <input type="hidden" name="action" value="<?php echo $id > 0 ? 'update' : 'create'; ?>">
            <?php if ($id > 0): ?>
                <input type="hidden" name="id" value="<?php echo $id; ?>">
            <?php endif; ?>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">모델명 <span class="text-danger">*</span></label>
                    <input type="text" name="model_name" class="form-control"
                        value="<?php echo h($asset['model_name']); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">자산번호 <span class="text-danger">*</span></label>
                    <input type="text" name="serial_number" class="form-control"
                        value="<?php echo h($asset['serial_number']); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">카테고리 <span class="text-danger">*</span></label>
                    <select name="category_id" class="form-select" required>
                        <option value="">선택하세요</option>
                        <?php foreach ($categories as $cat): ?>
                            <?php if (hasCategoryPermission($cat['name'])): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo $asset['category_id'] == $cat['id'] ? 'selected' : ''; ?>>
                                    <?php echo h($cat['name']); ?>
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">상태 <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="">선택하세요</option>
                        <option <?php echo $asset['status'] == '사용중' ? 'selected' : ''; ?>>사용중</option>
                        <option <?php echo $asset['status'] == '재고' ? 'selected' : ''; ?>>재고</option>
                        <option <?php echo $asset['status'] == '수리중' ? 'selected' : ''; ?>>수리중</option>
                        <option <?php echo $asset['status'] == '폐기' ? 'selected' : ''; ?>>폐기</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">중요도 <span class="text-danger">*</span></label>
                    <select name="importance" class="form-select" required>
                        <option value="High" <?php echo $asset['importance'] == 'High' ? 'selected' : ''; ?>>High</option>
                        <option value="Medium" <?php echo $asset['importance'] == 'Medium' ? 'selected' : ''; ?>>Medium
                        </option>
                        <option value="Low" <?php echo $asset['importance'] == 'Low' ? 'selected' : ''; ?>>Low</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">위험평가 <span class="text-danger">*</span></label>
                    <select name="risk_level" class="form-select" required>
                        <option value="High" <?php echo $asset['risk_level'] == 'High' ? 'selected' : ''; ?>>High</option>
                        <option value="Medium" <?php echo $asset['risk_level'] == 'Medium' ? 'selected' : ''; ?>>Medium
                        </option>
                        <option value="Low" <?php echo $asset['risk_level'] == 'Low' ? 'selected' : ''; ?>>Low</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">IP 주소</label>
                    <input type="text" name="ip_address" class="form-control"
                        value="<?php echo h($asset['ip_address']); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">VLAN 정보</label>
                    <input type="text" name="vlan_info" class="form-control"
                        value="<?php echo h($asset['vlan_info']); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">위치 (Rack No.)</label>
                    <input type="text" name="location" class="form-control"
                        value="<?php echo h($asset['location']); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">담당자</label>
                    <input type="text" name="manager_name" class="form-control"
                        value="<?php echo h($asset['manager_name']); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">도입일</label>
                    <input type="date" name="introduction_date" class="form-control"
                        value="<?php echo h($asset['introduction_date']); ?>">
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary px-4">저장하기</button>
                <a href="assets_list.php" class="btn btn-outline-secondary px-4">취소</a>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const serialInput = document.querySelector('input[name="serial_number"]');
        const assetId = <?php echo $id; ?>;

        if (serialInput) {
            serialInput.addEventListener('blur', function () {
                const serial = this.value.trim();
                if (serial === '') return;

                fetch(`check_serial.php?serial=${encodeURIComponent(serial)}&exclude_id=${assetId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.exists) {
                            alert('이미 등록된 자산번호입니다. 다른 번호를 입력해 주세요.');
                            this.value = '';
                            setTimeout(() => this.focus(), 10);
                        }
                    })
                    .catch(error => console.error('Error:', error));
            });
        }
    });
</script>

<?php include 'includes/footer.php'; ?>