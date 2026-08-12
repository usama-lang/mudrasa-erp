<?php

declare(strict_types=1);

namespace Modules\SchoolManagement\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class IslamicSchoolSeeder extends Seeder
{
    private \DateTimeImmutable $now;

    private array $pakistaniMaleNames = [
        'Muhammad Ali', 'Ahmad Hassan', 'Abdullah Malik', 'Usman Farooq', 'Ibrahim Sheikh',
        'Yusuf Qureshi', 'Omar Siddiqui', 'Bilal Ahmed', 'Hamza Raza', 'Zaid Khan',
        'Talha Awan', 'Saad Baig', 'Anas Mirza', 'Asim Chaudhry', 'Faisal Butt',
        'Tariq Mahmood', 'Adeel Hussain', 'Rizwan Akhtar', 'Kamran Sohail', 'Shahid Iqbal',
        'Imran Latif', 'Naveed Aslam', 'Junaid Zafar', 'Waseem Gondal', 'Sajid Noor',
        'Kashif Mehmood', 'Amjad Rauf', 'Khalid Niazi', 'Shafiq Ansari', 'Farhan Gilani',
        'Noman Ashraf', 'Shoaib Waqar', 'Arif Saleem', 'Ghulam Murtaza', 'Zulfiqar Ali',
        'Mudassar Javed', 'Tanveer Abbas', 'Iqbal Rehman', 'Salman Daud', 'Pervaiz Elahi',
        'Abdul Rehman', 'Muhammad Usman', 'Syed Adnan', 'Mohsin Raza', 'Waqas Nadeem',
    ];

    private array $fatherNames = [
        'Haji Muhammad Akram', 'Malik Ghulam Rasool', 'Chaudhry Aslam Khan', 'Syed Iftikhar',
        'Muhammad Bashir', 'Abdul Qayyum', 'Fazal ur Rehman', 'Khawaja Naseer',
        'Muhammad Ramzan', 'Hafiz Abdul Majeed', 'Mian Shafqat', 'Rana Tariq',
        'Dr. Zulfiqar', 'Engineer Khalid', 'Agha Imran', 'Sardar Naveed',
        'Muhammad Yousaf', 'Haji Ghulam Nabi', 'Maulana Saeed', 'Qari Hameed',
    ];

    private array $addresses = [
        'House #12, Street 4, Gulshan Iqbal, Lahore',
        'Plot 45, Block B, Model Town, Lahore',
        'Mohalla Islamabad, Gujranwala',
        'Village Chak 45, Faisalabad',
        'Near Jamia Mosque, Rawalpindi',
        'Sector G-10, Islamabad',
        'Bund Road, Lahore',
        'Shadman Colony, Lahore',
        'GT Road, Gujrat',
        'Cantt Area, Sialkot',
        'Old Anarkali, Lahore',
        'Johar Town, Lahore',
        'DHA Phase 4, Lahore',
        'Township, Lahore',
        'Samanabad, Lahore',
        'Raiwind Road, Lahore',
        'Multan Road, Lahore',
        'Ferozpur Road, Lahore',
        'Canal Road, Lahore',
        'Barkat Market, Lahore',
    ];

    public function run(): void
    {
        $this->now = new \DateTimeImmutable();

        DB::transaction(function () {
            $this->clearData();
            $superadmin = $this->getSuperadmin();
            $campuses   = $this->seedCampuses();
            $this->seedTeachers($campuses, $superadmin);
            $this->seedStudentsAndFees($campuses, $superadmin);
        });
    }

    private function clearData(): void
    {
        DB::table('school_fee_payments')->delete();
        DB::table('school_students')->delete();
        DB::table('school_sections')->delete();
        DB::table('school_classes')->delete();
        DB::table('school_departments')->delete();
        DB::table('school_campus_users')->delete();
        DB::table('school_teachers')->delete();
        DB::table('school_campuses')->delete();
    }

    private function getSuperadmin(): User
    {
        return User::where('email', 'superadmin@example.com')->first()
            ?? User::first()
            ?? User::factory()->create(['name' => 'Super Admin', 'email' => 'superadmin@example.com']);
    }

    // -------------------------------------------------------------------------
    // Campuses + Departments + Classes + Sections
    // -------------------------------------------------------------------------

    private function seedCampuses(): array
    {
        $campusDefs = [
            [
                'name'  => 'مرکزی کیمپس (Main Campus)',
                'code'  => 'C1',
                'phone' => '042-35761234',
                'email' => 'main@jamia.edu.pk',
                'departments' => [
                    ['name' => 'حفظ',        'classes' => ['دور اول', 'دور دوم', 'دور سوم', 'حفظ مکمل']],
                    ['name' => 'ناظرہ',      'classes' => ['قاعدہ', 'ناظرہ اول', 'ناظرہ دوم', 'ناظرہ سوم']],
                    ['name' => 'ترجمہ',      'classes' => ['ترجمہ اول', 'ترجمہ دوم', 'ترجمہ سوم']],
                    ['name' => 'قرآن گردان', 'classes' => ['قرآن گردان اول', 'قرآن گردان دوم']],
                ],
            ],
            [
                'name'  => 'شمالی کیمپس (North Campus)',
                'code'  => 'C2',
                'phone' => '042-36521234',
                'email' => 'north@jamia.edu.pk',
                'departments' => [
                    ['name' => 'حفظ',        'classes' => ['دور اول', 'دور دوم', 'دور سوم', 'حفظ مکمل']],
                    ['name' => 'ناظرہ',      'classes' => ['قاعدہ', 'ناظرہ اول', 'ناظرہ دوم', 'ناظرہ سوم']],
                    ['name' => 'ترجمہ',      'classes' => ['ترجمہ اول', 'ترجمہ دوم', 'ترجمہ سوم']],
                    ['name' => 'قرآن گردان', 'classes' => ['قرآن گردان اول', 'قرآن گردان دوم']],
                    ['name' => 'اسکول',      'classes' => ['کلاس اول', 'کلاس دوم', 'کلاس سوم', 'کلاس چہارم', 'کلاس پنجم', 'کلاس ششم', 'کلاس ہفتم', 'کلاس ہشتم']],
                ],
            ],
            [
                'name'  => 'جنوبی کیمپس (South Campus)',
                'code'  => 'C3',
                'phone' => '042-37891234',
                'email' => 'south@jamia.edu.pk',
                'departments' => [
                    ['name' => 'حفظ',        'classes' => ['دور اول', 'دور دوم', 'دور سوم', 'حفظ مکمل']],
                    ['name' => 'ناظرہ',      'classes' => ['قاعدہ', 'ناظرہ اول', 'ناظرہ دوم', 'ناظرہ سوم']],
                    ['name' => 'ترجمہ',      'classes' => ['ترجمہ اول', 'ترجمہ دوم', 'ترجمہ سوم']],
                    ['name' => 'قرآن گردان', 'classes' => ['قرآن گردان اول', 'قرآن گردان دوم']],
                    ['name' => 'اسکول',      'classes' => ['کلاس اول', 'کلاس دوم', 'کلاس سوم', 'کلاس چہارم', 'کلاس پنجم', 'کلاس ششم', 'کلاس ہفتم', 'کلاس ہشتم']],
                ],
            ],
        ];

        $result = [];
        $ts = $this->now->format('Y-m-d H:i:s');

        foreach ($campusDefs as $cd) {
            $campusId = DB::table('school_campuses')->insertGetId([
                'name'       => $cd['name'],
                'code'       => $cd['code'],
                'phone'      => $cd['phone'],
                'email'      => $cd['email'],
                'status'     => 'active',
                'created_at' => $ts,
                'updated_at' => $ts,
            ]);

            $depts = [];
            $level = 1;
            foreach ($cd['departments'] as $dept) {
                $deptId = DB::table('school_departments')->insertGetId([
                    'campus_id'  => $campusId,
                    'name'       => $dept['name'],
                    'status'     => 'active',
                    'created_at' => $ts,
                    'updated_at' => $ts,
                ]);

                $classes = [];
                foreach ($dept['classes'] as $className) {
                    $classId = DB::table('school_classes')->insertGetId([
                        'campus_id'    => $campusId,
                        'department_id'=> $deptId,
                        'name'         => $className,
                        'numeric_name' => $level++,
                        'status'       => 'active',
                        'created_at'   => $ts,
                        'updated_at'   => $ts,
                    ]);

                    $sections = [];
                    foreach (['الف', 'ب'] as $sec) {
                        $secId = DB::table('school_sections')->insertGetId([
                            'campus_id'  => $campusId,
                            'class_id'   => $classId,
                            'name'       => $sec,
                            'capacity'   => 25,
                            'status'     => 'active',
                            'created_at' => $ts,
                            'updated_at' => $ts,
                        ]);
                        $sections[] = $secId;
                    }

                    $classes[] = ['id' => $classId, 'sections' => $sections];
                }

                $depts[] = ['id' => $deptId, 'classes' => $classes];
            }

            $result[] = ['id' => $campusId, 'departments' => $depts];
        }

        return $result;
    }

    // -------------------------------------------------------------------------
    // Teachers
    // -------------------------------------------------------------------------

    private function seedTeachers(array $campuses, User $superadmin): void
    {
        $teachers = [
            ['first_name' => 'Hafiz',     'last_name' => 'Abdul Rehman',    'designation' => 'Principal',      'qualification' => 'M.A Arabic', 'salary' => 45000],
            ['first_name' => 'Qari',      'last_name' => 'Muhammad Yousaf', 'designation' => 'Senior Teacher',  'qualification' => 'Fazil',      'salary' => 35000],
            ['first_name' => 'Mufti',     'last_name' => 'Saeed Ahmad',     'designation' => 'Hifz Teacher',    'qualification' => 'Alim',       'salary' => 30000],
            ['first_name' => 'Qari',      'last_name' => 'Imran Khan',      'designation' => 'Nazra Teacher',   'qualification' => 'Fazil',      'salary' => 25000],
            ['first_name' => 'Maulana',   'last_name' => 'Tariq Jameel',    'designation' => 'Teacher',         'qualification' => 'Alim',       'salary' => 28000],
            ['first_name' => 'Hafiz',     'last_name' => 'Bilal Saeed',     'designation' => 'Hifz Teacher',    'qualification' => 'Hafiz',      'salary' => 22000],
            ['first_name' => 'Qari',      'last_name' => 'Asim Baig',       'designation' => 'Teacher',         'qualification' => 'Fazil',      'salary' => 24000],
        ];

        $ts = $this->now->format('Y-m-d H:i:s');
        $empCounter = 1;

        foreach ($campuses as $campus) {
            foreach ($teachers as $t) {
                $user = User::create([
                    'first_name' => $t['first_name'],
                    'last_name'  => $t['last_name'],
                    'email'      => 'teacher' . $empCounter . '_c' . $campus['id'] . '@jamia.edu.pk',
                    'password'   => Hash::make('password'),
                    'username'   => 'teacher_' . $empCounter . '_c' . $campus['id'],
                ]);

                DB::table('school_teachers')->insert([
                    'campus_id'    => $campus['id'],
                    'user_id'      => $user->id,
                    'employee_id'  => 'EMP-' . str_pad((string) $empCounter, 4, '0', STR_PAD_LEFT),
                    'designation'  => $t['designation'],
                    'qualification'=> $t['qualification'],
                    'salary'       => $t['salary'],
                    'joining_date' => $this->now->modify('-' . rand(6, 36) . ' months')->format('Y-m-d'),
                    'status'       => 'active',
                    'created_at'   => $ts,
                    'updated_at'   => $ts,
                ]);

                $empCounter++;
            }
        }
    }

    // -------------------------------------------------------------------------
    // Students + Fee Payments
    // -------------------------------------------------------------------------

    private function seedStudentsAndFees(array $campuses, User $superadmin): void
    {
        $ts = $this->now->format('Y-m-d H:i:s');
        $admissionCounter = 1;
        $receiptCounter   = 1;

        $enrollmentStatuses = ['enrolled', 'enrolled', 'enrolled', 'enrolled', 'left_self', 'completed'];
        $paymentMethods     = ['cash', 'cash', 'cash', 'bank_transfer', 'cheque'];

        foreach ($campuses as $campus) {
            foreach ($campus['departments'] as $dept) {
                foreach ($dept['classes'] as $class) {
                    // 4–8 students per class
                    $count = rand(4, 8);
                    for ($i = 0; $i < $count; $i++) {
                        $name           = $this->pakistaniMaleNames[array_rand($this->pakistaniMaleNames)];
                        $fatherName     = $this->fatherNames[array_rand($this->fatherNames)];
                        $address        = $this->addresses[array_rand($this->addresses)];
                        $sectionId      = $class['sections'][array_rand($class['sections'])];
                        $isResident     = (rand(0, 2) === 0); // ~33% resident
                        $foodAtMadrasa  = !$isResident && (rand(0, 1) === 1); // non-resident may eat at madrasa
                        $foodCharges    = $foodAtMadrasa ? (float) rand(200, 500) : 0.0;
                        $monthlyFee     = (float) (rand(3, 12) * 100); // 300–1200 PKR
                        $enrollStatus   = $enrollmentStatuses[array_rand($enrollmentStatuses)];
                        $admissionDate  = $this->now->modify('-' . rand(6, 36) . ' months')->format('Y-m-d');
                        $dob            = $this->now->modify('-' . rand(8, 18) . ' years')->format('Y-m-d');
                        $phone          = '03' . rand(0, 4) . rand(0, 9) . '-' . rand(1000000, 9999999);
                        $cnic           = str_pad((string) rand(1000, 9999), 5, '0') . '-' . str_pad((string) rand(1000000, 9999999), 7, '0') . '-' . rand(1, 9);
                        $admNo          = 'ADM-' . date('Y') . '-' . str_pad((string) $admissionCounter++, 4, '0', STR_PAD_LEFT);

                        $leavingDate  = null;
                        $statusRemark = null;
                        if ($enrollStatus !== 'enrolled') {
                            $leavingDate  = $this->now->modify('-' . rand(1, 6) . ' months')->format('Y-m-d');
                            $statusRemark = match ($enrollStatus) {
                                'left_self' => 'والدین نے خود چھڑوایا',
                                'completed' => 'کامیابی سے مکمل کیا',
                                'expelled'  => 'غیر حاضری کی وجہ سے',
                                default     => 'وجہ معلوم نہیں',
                            };
                        }

                        $studentId = DB::table('school_students')->insertGetId([
                            'campus_id'         => $campus['id'],
                            'department_id'     => $dept['id'],
                            'class_id'          => $class['id'],
                            'section_id'        => $sectionId,
                            'user_id'           => null,
                            'admission_no'      => $admNo,
                            'admission_date'    => $admissionDate,
                            'student_name'      => $name,
                            'father_name'       => $fatherName,
                            'father_cnic'       => $cnic,
                            'phone_no'          => $phone,
                            'current_address'   => $address,
                            'permanent_address' => $address,
                            'date_of_birth'     => $dob,
                            'monthly_fee'       => $monthlyFee,
                            'residential_status'=> $isResident ? 'resident' : 'non_resident',
                            'food_at_madrasa'   => $foodAtMadrasa ? 1 : 0,
                            'food_charges'      => $foodCharges,
                            'enrollment_status' => $enrollStatus,
                            'leaving_date'      => $leavingDate,
                            'status_remarks'    => $statusRemark,
                            'status'            => $enrollStatus === 'enrolled' ? 1 : 0,
                            'created_at'        => $ts,
                            'updated_at'        => $ts,
                        ]);

                        // Fee payments for the last 4 months (only enrolled students)
                        if ($enrollStatus === 'enrolled') {
                            $paidMonths = rand(1, 4);
                            for ($m = $paidMonths; $m >= 1; $m--) {
                                $feeMonth     = $this->now->modify("-{$m} months")->format('Y-m-01');
                                $payDate      = $this->now->modify("-{$m} months")->modify('+' . rand(1, 10) . ' days')->format('Y-m-d');
                                $discount     = rand(0, 3) === 0 ? (float) rand(50, 200) : 0.0;
                                $totalDue     = $monthlyFee + $foodCharges;
                                $isPaid       = rand(0, 4) > 0; // 80% fully paid
                                $amountPaid   = $isPaid ? ($totalDue - $discount) : ($totalDue * 0.5);
                                $balance      = $totalDue - $discount - $amountPaid;
                                $status       = $balance <= 0 ? 'paid' : 'partial';
                                $receiptNo    = 'RCP-' . $this->now->modify("-{$m} months")->format('Ym') . '-' . str_pad((string) $receiptCounter++, 4, '0', STR_PAD_LEFT);
                                $method       = $paymentMethods[array_rand($paymentMethods)];

                                DB::table('school_fee_payments')->insert([
                                    'student_id'     => $studentId,
                                    'campus_id'      => $campus['id'],
                                    'department_id'  => $dept['id'],
                                    'class_id'       => $class['id'],
                                    'section_id'     => $sectionId,
                                    'fee_month'      => $feeMonth,
                                    'monthly_fee'    => $monthlyFee,
                                    'food_charges'   => $foodCharges,
                                    'amount_paid'    => $amountPaid,
                                    'discount'       => $discount,
                                    'balance'        => max(0, $balance),
                                    'payment_date'   => $payDate,
                                    'payment_method' => $method,
                                    'receipt_no'     => $receiptNo,
                                    'collected_by'   => $superadmin->id,
                                    'notes'          => null,
                                    'status'         => $status,
                                    'created_at'     => $ts,
                                    'updated_at'     => $ts,
                                ]);
                            }
                        }
                    }
                }
            }
        }
    }
}
