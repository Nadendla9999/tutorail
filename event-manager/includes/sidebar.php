<div class="row">
    <div class="col-lg-3 col-md-4">
        <div class="card shadow-sm mb-4" data-aos="fade-right" data-aos-duration="1000">
            <div class="card-header bg-gradient" style="background: linear-gradient(to right, var(--primary-color), #3a5ccc);">
                <h5 class="card-title mb-0 text-white">
                    <i class="bi bi-list me-2"></i>Menu
                </h5>
            </div>
            <div class="card-body p-0">
                <ul class="nav flex-column">
                    <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center py-3 px-3 border-bottom menu-item" href="../admin/dashboard.php">
                                <i class="bi bi-speedometer2 me-2"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center py-3 px-3 border-bottom menu-item" href="../admin/manage_halls.php">
                                <i class="bi bi-building me-2"></i>
                                <span>Function Halls</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center py-3 px-3 border-bottom menu-item" href="../admin/manage_caterers.php">
                                <i class="bi bi-cup-hot me-2"></i>
                                <span>Caterers</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center py-3 px-3 border-bottom menu-item" href="../admin/manage_decorations.php">
                                <i class="bi bi-stars me-2"></i>
                                <span>Decorations</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center py-3 px-3 border-bottom menu-item" href="../admin/manage_djs.php">
                                <i class="bi bi-music-note-beamed me-2"></i>
                                <span>DJ Sets</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center py-3 px-3 border-bottom menu-item" href="../admin/assign_events.php">
                                <i class="bi bi-calendar2-check me-2"></i>
                                <span>Assign Events</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center py-3 px-3 menu-item" href="../admin/view_performance.php">
                                <i class="bi bi-graph-up me-2"></i>
                                <span>Manager Performance</span>
                            </a>
                        </li>
                    <?php elseif(isset($_SESSION['role']) && $_SESSION['role'] == 'manager'): ?>
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center py-3 px-3 border-bottom menu-item" href="../manager/dashboard.php">
                                <i class="bi bi-speedometer2 me-2"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center py-3 px-3 menu-item" href="../manager/assigned_events.php">
                                <i class="bi bi-calendar2-event me-2"></i>
                                <span>Assigned Events</span>
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center py-3 px-3 border-bottom menu-item" href="../user/dashboard.php">
                                <i class="bi bi-speedometer2 me-2"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center py-3 px-3 border-bottom menu-item" href="../user/browse_services.php">
                                <i class="bi bi-search me-2"></i>
                                <span>Browse Services</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center py-3 px-3 border-bottom menu-item" href="../user/create_event.php">
                                <i class="bi bi-calendar-plus me-2"></i>
                                <span>Create Event</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center py-3 px-3 menu-item" href="../user/my_events.php">
                                <i class="bi bi-calendar-check me-2"></i>
                                <span>My Events</span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-lg-9 col-md-8">
        <!-- Sidebar content ends here, main content starts -->
        
<style>
    /* Menu item hover effect */
    .menu-item {
        transition: all 0.3s ease;
        color: var(--dark-color);
        border-left: 3px solid transparent;
    }
    
    .menu-item:hover {
        background-color: rgba(78, 115, 223, 0.1);
        color: var(--primary-color);
        border-left: 3px solid var(--primary-color);
        transform: translateX(5px);
    }
    
    /* Active menu item */
    .menu-item.active {
        background-color: rgba(78, 115, 223, 0.15);
        color: var(--primary-color);
        border-left: 3px solid var(--primary-color);
        font-weight: 500;
    }
    
    /* Make sidebar sticky on larger screens */
    @media (min-width: 992px) {
        .sticky-sidebar {
            position: sticky;
            top: 20px;
        }
    }
    
    /* Responsive adjustments */
    @media (max-width: 767.98px) {
        .card-header h5 {
            font-size: 1rem;
        }
        
        .nav-link {
            padding: 0.5rem 0.75rem;
            font-size: 0.9rem;
        }
    }
</style>

<script>
    // Add active class to current menu item
    document.addEventListener('DOMContentLoaded', function() {
        const currentLocation = window.location.pathname;
        const menuItems = document.querySelectorAll('.menu-item');
        
        menuItems.forEach(item => {
            if (item.getAttribute('href') && currentLocation.includes(item.getAttribute('href'))) {
                item.classList.add('active');
            }
        });
    });
</script>