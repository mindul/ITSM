<?php
require_once 'config/db.php';
require_once 'includes/functions.php';
include 'includes/header.php';

// Fetch network and security assets for topology
$query = "SELECT a.id, a.model_name, a.serial_number, c.name as category 
          FROM assets a 
          JOIN categories c ON a.category_id = c.id 
          WHERE c.name IN ('네트워크장비', '정보보호시스템')";
$net_assets = $pdo->query($query)->fetchAll();

// Mock topology connections (In a real scenario, these would be in a junction table)
$connections = [
    ['from' => 'FW-01', 'to' => 'SW-Core'],
    ['from' => 'SW-Core', 'to' => 'SW-Workgroup-01'],
    ['from' => 'SW-Core', 'to' => 'SW-Workgroup-02'],
    ['from' => 'SW-Workgroup-01', 'to' => 'Server-V-01'],
    ['from' => 'SW-Workgroup-01', 'to' => 'Server-V-02'],
];
?>

<div class="pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">네트워크 토폴로지</h1>
</div>

<div class="row">
    <div class="col-md-9 mb-4">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">장비 연결 관계 (Visualization)</h5>
                <button class="btn btn-sm btn-outline-primary" onclick="location.reload()"><i
                        class="fas fa-sync-alt"></i></button>
            </div>
            <div class="card-body">
                <div class="mermaid text-center">
                    graph TD
                    FW["Firewall (External)"] --> CORE["Core Switch"]
                    CORE --> SW1["L2 Switch (Floor 1)"]
                    CORE --> SW2["L2 Switch (Floor 2)"]

                    <?php
                    // Dynamically add servers to the diagram for demo purposes
                    $servers = $pdo->query("SELECT model_name FROM assets WHERE category_id = (SELECT id FROM categories WHERE name = '서버') LIMIT 4")->fetchAll();
                    foreach ($servers as $index => $srv) {
                        $nodeId = "Srv" . $index;
                        echo "SW1 --> $nodeId" . '["' . h($srv['model_name']) . '"]' . "\n";
                    }
                    ?>

                    style FW fill:#f96,stroke:#333,stroke-width:2px
                    style CORE fill:#69f,stroke:#333,stroke-width:2px
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">장비 범례</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li class="mb-2"><span class="badge" style="background-color: #f96; color:transparent; width: 20px;">_</span> 정보보호시스템</li>
                    <li class="mb-2"><span class="badge" style="background-color: #69f; color:transparent; width: 20px;">_</span> 네트워크장비</li>
                    <li class="mb-2"><span class="badge bg-secondary" style="color:transparent; width: 20px;">_</span> 기타장비/서버</li>
                </ul>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">주요 장비 정보</h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php if (empty($net_assets)): ?>
                        <div class="p-3 text-muted small text-center">장비 정보가 없습니다.</div>
                    <?php else: ?>
                        <?php foreach (array_slice($net_assets, 0, 5) as $net): ?>
                            <div class="list-group-item">
                                <h6 class="mb-1 small fw-bold">
                                    <?php echo h($net['model_name']); ?>
                                </h6>
                                <small class="text-muted">
                                    <?php echo h($net['serial_number']); ?>
                                </small>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mermaid.js -->
<script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js"></script>
<script>
    mermaid.initialize({ startOnLoad: true, theme: 'default' });
</script>

<?php include 'includes/footer.php'; ?>