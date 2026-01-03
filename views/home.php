<?php
$pageTitle = 'Kasr Al Ainy Hospital - Home';
include __DIR__ . '/layouts/header.php';
?>

<!-- Main Content -->
<main>
    <!-- ========== HOME PAGE ========== -->
    <div id="home-page" class="page active">
        <!-- Hero Section -->
        <section class="home-hero">
            <div class="container">
                <div class="hero-content">
                    <h1>مستشفى طب القصر العيني</h1>
                    <p>
                        يُعد مستشفى طب القصر العيني أحد أعرق الصروح الطبية في مصر والشرق الأوسط،
                        حيث يجمع بين الخبرة الطبية العريقة وأحدث التقنيات الحديثة في تقديم
                        الرعاية الصحية. يهدف نظام إدارة المستشفى إلى تنظيم الخدمات الطبية
                        وتسهيل الإجراءات وتحسين تجربة المرضى والأطباء.
                    </p>
                    <div class="hero-buttons">
                        <a href="<?php echo APP_URL; ?>/public/index.php?page=login" id="home-login-btn" class="btn btn-primary">
                            تسجيل الدخول إلى النظام
                        </a>
                        <a href="<?php echo APP_URL; ?>/public/index.php?page=register" id="home-signup-btn" class="btn btn-outline">
                            إنشاء حساب جديد
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="section">
            <div class="container">
                <div class="section-title">
                    <h2>نظام متكامل لإدارة الرعاية الصحية</h2>
                    <p>
                        يوفر نظام إدارة مستشفى طب القصر العيني حلولًا رقمية متطورة
                        تساعد في رفع كفاءة العمل الطبي والإداري.
                    </p>
                </div>
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <h3>إدارة الأطباء</h3>
                        <p>
                            إدارة بيانات الأطباء، التخصصات، والجداول الزمنية
                            من خلال واجهة سهلة الاستخدام.
                        </p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <h3>حجز المواعيد</h3>
                        <p>
                            تمكين المرضى من حجز المواعيد، تعديلها أو إلغائها
                            إلكترونيًا بكل سهولة.
                        </p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <h3>تنظيم الجداول الطبية</h3>
                        <p>
                            تنسيق مواعيد الأطباء وتتبع الجلسات الطبية
                            لضمان أفضل استغلال للوقت.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- User Roles Section -->
        <section class="section roles-section">
            <div class="container">
                <div class="section-title">
                    <h2>أدوار المستخدمين</h2>
                    <p>
                        تم تصميم النظام ليخدم جميع أطراف المنظومة الطبية
                        وفقًا لدور كل مستخدم.
                    </p>
                </div>
                <div class="roles-grid">
                    <div class="role-card">
                        <div class="role-icon admin">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <h3>الإدارة</h3>
                        <p>تحكم شامل في النظام وإدارة العمليات</p>
                        <ul>
                            <li>إدارة الأطباء والأقسام</li>
                            <li>متابعة جميع المواعيد</li>
                            <li>إنشاء التقارير الطبية والإدارية</li>
                            <li>ضبط إعدادات النظام</li>
                        </ul>
                    </div>
                    <div class="role-card">
                        <div class="role-icon doctor">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <h3>الطبيب</h3>
                        <p>بيئة رقمية منظمة لدعم الرعاية الطبية</p>
                        <ul>
                            <li>الاطلاع على جدول المواعيد</li>
                            <li>إدارة أوقات التواجد</li>
                            <li>تحديث السجلات الطبية</li>
                            <li>متابعة الحالات المرضية</li>
                        </ul>
                    </div>
                    <div class="role-card">
                        <div class="role-icon patient">
                            <i class="fas fa-user-injured"></i>
                        </div>
                        <h3>المريض</h3>
                        <p>خدمات صحية سهلة وسريعة</p>
                        <ul>
                            <li>حجز المواعيد إلكترونيًا</li>
                            <li>استعراض بيانات الأطباء</li>
                            <li>إدارة المواعيد</li>
                            <li>الوصول للمعلومات الطبية</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="section cta-section">
            <div class="container">
                <h2>نحو مستقبل رقمي للرعاية الصحية</h2>
                <p>
                    انضم إلى منظومة طب القصر العيني الرقمية، حيث تلتقي
                    الخبرة الطبية بالتكنولوجيا الحديثة لتقديم أفضل
                    مستوى من الرعاية الصحية.
                </p>
                <div class="hero-buttons">
                    <a href="<?php echo APP_URL; ?>/public/index.php?page=login" id="cta-login-btn" class="btn btn-primary">
                        ابدأ الآن
                    </a>
                    <a href="#" id="cta-learn-more" class="btn btn-outline" style="border-color: var(--white); color: var(--white);">
                        تعرّف أكثر على النظام
                    </a>
                </div>
            </div>
        </section>
    </div>
</main>

<?php include __DIR__ . '/layouts/footer.php'; ?>
