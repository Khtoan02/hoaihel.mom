<?php
/**
 * Template Name: Khảo sát CFD
 * Description: Trang khảo sát Thiếu Folate Não.
 *
 * @package hoaihel-mom
 */

wp_enqueue_script('hoaihel-tailwind', 'https://cdn.tailwindcss.com', [], null, false);
wp_enqueue_style('hoaihel-fa', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css', [], '6.0.0');

$survey_ajax_config = [
    'ajaxUrl' => admin_url('admin-ajax.php'),
    'nonce'   => wp_create_nonce('hoaihel_survey_nonce'),
];

get_header();
?>

<style>
    .page-template-khao-sat-template .hhm-site-header,
    .page-template-khao-sat-template .hhm-site-footer {
        display: none;
    }
    .page-template-khao-sat-template .survey-shell {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }
    .page-template-khao-sat-template .survey-shell main {
        flex: 1;
    }
    body { font-family: 'Inter', sans-serif; }
    input:checked + label {
        border-color: #9333ea !important;
        background-color: #faf5ff !important;
        color: #7e22ce !important;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }
    input:checked + label .icon-box {
        background-color: #9333ea !important;
        border-color: #9333ea !important;
    }
    input:checked + label .check-icon {
        opacity: 1 !important;
    }
    .fade-in { animation: fadeIn 0.5s ease-in-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>

<section class="bg-slate-50 text-slate-700 antialiased selection:bg-purple-200 selection:text-purple-900">
<div class="survey-shell">

    <header class="bg-white shadow-sm sticky top-0 z-40 border-t-4 border-purple-600">
        <div class="max-w-4xl mx-auto px-4 py-4 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <i class="fa-solid fa-brain text-purple-600 text-2xl"></i>
                <h1 class="text-lg font-bold text-slate-800 hidden sm:block">Khảo sát CFD & ASD</h1>
                <h1 class="text-lg font-bold text-slate-800 sm:hidden">Khảo sát CFD</h1>
            </div>
            <div class="text-sm text-slate-500 font-medium">Hãy dành 10 phút để điền vào form bên dưới. Mọi thông tin đều được bảo mật và chỉ sử dụng cho mục đích hỗ trợ chuyên môn. </div>
        </div>
    </header>

    <main class="max-w-3xl mx-auto px-4 py-8 mb-20">
        
        <!-- Intro Card -->
        <div id="introSection" class="bg-white rounded-2xl shadow-lg p-6 mb-8 border-l-4 border-purple-500 fade-in">
            <h2 class="text-2xl font-bold text-slate-800 mb-4 leading-tight text-center sm:text-left text-purple-800">SAU CHUỖI BÀI VỀ "NÃO ĐÓI FOLATE" - BẠN ĐANG LO LẮNG ĐIỀU GÌ VỀ CON MÌNH?</h2>
            <div class="prose text-slate-600 mb-6 text-sm sm:text-base text-justify">
                <p class="mb-2">Chào các cha mẹ,</p>
                <p class="mb-2">Sau loạt 7 bài viết chuyên sâu về <strong>Thiếu Folate Não (CFD)</strong> và điều trị bằng <strong>Leucovorin</strong>, chúng tôi hiểu rằng giữa "bão” thông tin hiện nay, việc tìm kiếm một lộ trình hỗ trợ con đúng đắn và khoa học là vô cùng khó khăn.</p>
                <p class="mb-2">Để giúp kết nối cha mẹ với những giải đáp chính xác nhất từ góc nhìn <strong>Y KHOA CHUYÊN BIỆT</strong>, chúng tôi thực hiện khảo sát ngắn này.</p>
                <p>Những chia sẻ của bạn sẽ là cơ sở để chúng tôi tổng hợp và gửi đến các bác sỹ chuyên khoa thần kinh, y sinh cho trẻ tự kỷ để nhờ họ giải đáp.</p>
            </div>
            <button onclick="startSurvey()" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-6 rounded-lg transition duration-300 flex justify-center items-center shadow-md ring-4 ring-purple-100">
                <span>Bắt đầu Khảo sát</span>
                <i class="fa-solid fa-arrow-right ml-2"></i>
            </button>
            <div class="mt-4 text-center text-xs text-slate-400">
                <i class="fa-solid fa-shield-halved mr-1"></i> Mọi thông tin đều được bảo mật.
            </div>
        </div>

        <!-- Survey Form Container (Hidden initially) -->
        <form id="surveyForm" class="hidden" onsubmit="event.preventDefault(); submitSurvey();">
            
            <!-- Progress Bar -->
            <div class="mb-6 sticky top-[72px] bg-slate-50 pt-2 z-40 pb-2">
                <div class="flex justify-between mb-2">
                    <span class="text-xs font-bold text-purple-700 uppercase" id="stepLabel">Phần 1/3</span>
                    <span class="text-xs font-bold text-purple-700" id="progressPercent">33%</span>
                </div>
                <div class="overflow-hidden h-2.5 mb-2 text-xs flex rounded-full bg-slate-200">
                    <div id="progressBar" style="width: 33%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-gradient-to-r from-purple-400 to-purple-600 transition-all duration-500 rounded-full"></div>
                </div>
            </div>

            <!-- PART 1 -->
            <div id="step1" class="step fade-in">
                <div class="bg-white rounded-xl shadow-md p-6 border border-slate-100">
                    <h3 class="text-xl font-bold text-purple-800 mb-4 border-b pb-2 flex items-center">
                        <span class="bg-purple-100 text-purple-600 w-8 h-8 rounded-full flex items-center justify-center text-sm mr-2">1</span>
                        QUAN SÁT & ĐÁNH GIÁ
                    </h3>
                    
                    <!-- Câu 1 -->
                    <div class="mb-6">
                        <label class="block text-slate-800 font-semibold mb-3 text-base">
                            1. Sau khi tìm hiểu về triệu chứng Thiếu Folate Não (CFD), bạn có quan sát thấy con mình từng hoặc đang có các dấu hiệu nào dưới đây không? <span class="text-sm font-normal text-slate-500 italic block mt-1">(Có thể chọn nhiều)</span>
                        </label>
                        <div class="space-y-2">
                            <!-- Checkbox Options - Added 'icon-box' class to inner divs -->
                            <div class="option-item">
                                <input type="checkbox" id="c1_1" name="symptoms" value="Thoái triển phát triển" class="hidden peer">
                                <label for="c1_1" class="flex items-start p-3 bg-white border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50 transition">
                                    <div class="icon-box flex-shrink-0 mt-0.5 w-5 h-5 border-2 border-slate-300 rounded mr-3 flex items-center justify-center bg-white">
                                        <i class="fa-solid fa-check text-white text-xs opacity-0 check-icon transition-opacity duration-200"></i>
                                    </div>
                                    <span class="text-sm text-slate-700">Thoái triển phát triển (Mất dần kỹ năng ngôn ngữ/vận động)</span>
                                </label>
                            </div>
                            
                            <div class="option-item">
                                <input type="checkbox" id="c1_2" name="symptoms" value="Động kinh hoặc co giật" class="hidden peer">
                                <label for="c1_2" class="flex items-start p-3 bg-white border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50 transition">
                                    <div class="icon-box flex-shrink-0 mt-0.5 w-5 h-5 border-2 border-slate-300 rounded mr-3 flex items-center justify-center bg-white">
                                        <i class="fa-solid fa-check text-white text-xs opacity-0 check-icon transition-opacity duration-200"></i>
                                    </div>
                                    <span class="text-sm text-slate-700">Động kinh hoặc co giật (kể cả những cơn vắng ý thức nhỏ)</span>
                                </label>
                            </div>

                            <div class="option-item">
                                <input type="checkbox" id="c1_3" name="symptoms" value="Giảm trương lực cơ/Mất điều hòa" class="hidden peer">
                                <label for="c1_3" class="flex items-start p-3 bg-white border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50 transition">
                                    <div class="icon-box flex-shrink-0 mt-0.5 w-5 h-5 border-2 border-slate-300 rounded mr-3 flex items-center justify-center bg-white">
                                        <i class="fa-solid fa-check text-white text-xs opacity-0 check-icon transition-opacity duration-200"></i>
                                    </div>
                                    <span class="text-sm text-slate-700">Giảm trương lực cơ (mềm nhão) hoặc mất điều hòa vận động</span>
                                </label>
                            </div>

                            <div class="option-item">
                                <input type="checkbox" id="c1_4" name="symptoms" value="Cáu kỉnh, rối loạn giấc ngủ" class="hidden peer">
                                <label for="c1_4" class="flex items-start p-3 bg-white border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50 transition">
                                    <div class="icon-box flex-shrink-0 mt-0.5 w-5 h-5 border-2 border-slate-300 rounded mr-3 flex items-center justify-center bg-white">
                                        <i class="fa-solid fa-check text-white text-xs opacity-0 check-icon transition-opacity duration-200"></i>
                                    </div>
                                    <span class="text-sm text-slate-700">Cáu kỉnh nghiêm trọng, la hét, rối loạn giấc ngủ nặng</span>
                                </label>
                            </div>

                             <div class="option-item">
                                <input type="checkbox" id="c1_5" name="symptoms" value="Tật đầu nhỏ mắc phải" class="hidden peer">
                                <label for="c1_5" class="flex items-start p-3 bg-white border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50 transition">
                                    <div class="icon-box flex-shrink-0 mt-0.5 w-5 h-5 border-2 border-slate-300 rounded mr-3 flex items-center justify-center bg-white">
                                        <i class="fa-solid fa-check text-white text-xs opacity-0 check-icon transition-opacity duration-200"></i>
                                    </div>
                                    <span class="text-sm text-slate-700">Kích thước vòng đầu phát triển chậm lại (Tật đầu nhỏ mắc phải)</span>
                                </label>
                            </div>

                            <div class="option-item">
                                <input type="checkbox" id="c1_6" name="symptoms" value="Không rõ ràng" class="hidden peer">
                                <label for="c1_6" class="flex items-start p-3 bg-white border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50 transition">
                                    <div class="icon-box flex-shrink-0 mt-0.5 w-5 h-5 border-2 border-slate-300 rounded mr-3 flex items-center justify-center bg-white">
                                        <i class="fa-solid fa-check text-white text-xs opacity-0 check-icon transition-opacity duration-200"></i>
                                    </div>
                                    <span class="text-sm text-slate-700">Không thấy các dấu hiệu trên rõ ràng</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Câu 2 -->
                    <div class="mb-4">
                        <label class="block text-slate-800 font-semibold mb-3">
                            2. Con bạn đã bao giờ được thăm khám chuyên sâu để đánh giá về các vấn đề liên quan đến Folate chưa?
                        </label>
                        <div class="space-y-2">
                             <div class="relative">
                                <select id="c2" name="diagnosis_history" class="block w-full p-3 border border-slate-300 rounded-lg bg-white text-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                    <option value="" disabled selected>-- Vui lòng chọn --</option>
                                    <option value="Chưa từng">Chưa từng thăm khám chuyên sâu, chỉ khám sức khỏe định kỳ</option>
                                    <option value="Xét nghiệm máu">Đã xét nghiệm máu thông thường (Folate huyết thanh, B12...)</option>
                                    <option value="Xét nghiệm gen">Đã xét nghiệm gen (MTHFR, FOLR1...)</option>
                                    <option value="Xét nghiệm FRAA">Đã xét nghiệm tìm Tự kháng thể thụ thể Folate (FRAA)</option>
                                    <option value="Chọc dò dịch não tủy">Đã chọc dò dịch não tủy (CSF) để đo 5-MTHF</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end mt-8">
                        <button type="button" onclick="nextStep(2)" class="bg-purple-600 text-white px-6 py-2.5 rounded-lg hover:bg-purple-700 transition flex items-center shadow-lg shadow-purple-200 font-medium">
                            Tiếp tục <i class="fa-solid fa-chevron-right ml-2"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- PART 2 -->
            <div id="step2" class="step hidden fade-in">
                <div class="bg-white rounded-xl shadow-md p-6 border border-slate-100">
                    <h3 class="text-xl font-bold text-purple-800 mb-4 border-b pb-2 flex items-center">
                        <span class="bg-purple-100 text-purple-600 w-8 h-8 rounded-full flex items-center justify-center text-sm mr-2">2</span>
                        NHẬN THỨC & ĐIỀU TRỊ
                    </h3>

                    <!-- Câu 3 -->
                    <div class="mb-6">
                        <label class="block text-slate-800 font-semibold mb-3">
                            3. Hiện tại, bạn có đang bổ sung bất kỳ sản phẩm nào liên quan đến Folate cho con không?
                        </label>
                        <div class="space-y-2">
                             <div class="option-item">
                                <input type="radio" id="c3_1" name="current_treatment" value="Không" class="hidden peer">
                                <label for="c3_1" class="flex items-center p-3 bg-white border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50 transition">
                                    <div class="icon-box w-4 h-4 rounded-full border border-slate-300 mr-3 flex items-center justify-center bg-white">
                                        <div class="w-2 h-2 rounded-full bg-white opacity-0 check-icon transition-opacity duration-200"></div>
                                    </div>
                                    <span class="text-sm text-slate-700">Không bổ sung gì</span>
                                </label>
                            </div>
                            <div class="option-item">
                                <input type="radio" id="c3_2" name="current_treatment" value="Vitamin tổng hợp (Axit Folic)" class="hidden peer">
                                <label for="c3_2" class="flex items-center p-3 bg-white border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50 transition">
                                    <div class="icon-box w-4 h-4 rounded-full border border-slate-300 mr-3 flex items-center justify-center bg-white">
                                        <div class="w-2 h-2 rounded-full bg-white opacity-0 check-icon transition-opacity duration-200"></div>
                                    </div>
                                    <span class="text-sm text-slate-700">Có, đang dùng Vitamin tổng hợp có chứa Axit Folic</span>
                                </label>
                            </div>
                            <div class="option-item">
                                <input type="radio" id="c3_3" name="current_treatment" value="5-MTHF" class="hidden peer">
                                <label for="c3_3" class="flex items-center p-3 bg-white border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50 transition">
                                    <div class="icon-box w-4 h-4 rounded-full border border-slate-300 mr-3 flex items-center justify-center bg-white">
                                        <div class="w-2 h-2 rounded-full bg-white opacity-0 check-icon transition-opacity duration-200"></div>
                                    </div>
                                    <span class="text-sm text-slate-700">Có, đang dùng 5-MTHF (L-Methylfolate)</span>
                                </label>
                            </div>
                            <div class="option-item">
                                <input type="radio" id="c3_4" name="current_treatment" value="Folinic Acid TPCN" class="hidden peer">
                                <label for="c3_4" class="flex items-center p-3 bg-white border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50 transition">
                                    <div class="icon-box w-4 h-4 rounded-full border border-slate-300 mr-3 flex items-center justify-center bg-white">
                                        <div class="w-2 h-2 rounded-full bg-white opacity-0 check-icon transition-opacity duration-200"></div>
                                    </div>
                                    <span class="text-sm text-slate-700">Có, đang dùng Folinic Acid dạng TPCN (xách tay/không kê đơn)</span>
                                </label>
                            </div>
                             <div class="option-item">
                                <input type="radio" id="c3_5" name="current_treatment" value="Thuốc Leucovorin" class="hidden peer">
                                <label for="c3_5" class="flex items-center p-3 bg-white border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50 transition">
                                    <div class="icon-box w-4 h-4 rounded-full border border-slate-300 mr-3 flex items-center justify-center bg-white">
                                        <div class="w-2 h-2 rounded-full bg-white opacity-0 check-icon transition-opacity duration-200"></div>
                                    </div>
                                    <span class="text-sm text-slate-700">Có, đang dùng thuốc Leucovorin theo đơn bác sĩ</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Câu 4 -->
                    <div class="mb-6">
                        <label class="block text-slate-800 font-semibold mb-3">
                            4. Theo hiểu biết của bạn, sự khác biệt giữa "Folinic Acid dạng TPCN" và "Thuốc Leucovorin điều trị CFD" là gì?
                        </label>
                        <select id="c4" name="knowledge_gap" class="block w-full p-3 border border-slate-300 rounded-lg bg-white text-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                             <option value="" disabled selected>-- Chọn quan điểm của bạn --</option>
                             <option value="Giống hệt nhau">Chúng giống hệt nhau, chỉ khác tên gọi.</option>
                             <option value="Leucovorin liều cao tinh khiết">Thuốc Leucovorin có liều lượng cao hơn, tinh khiết hơn, cần kê đơn.</option>
                             <option value="Chưa phân biệt được">Tôi chưa phân biệt được hai loại này.</option>
                             <option value="TPCN an toàn hơn">Tôi nghĩ thực phẩm bổ sung an toàn hơn thuốc.</option>
                        </select>
                    </div>

                    <!-- Câu 5 -->
                     <div class="mb-4">
                        <label class="block text-slate-800 font-semibold mb-3">
                            5. Quan điểm của bạn về vai trò của Bác sĩ chuyên khoa trong việc điều trị Thiếu Folate Não?
                        </label>
                         <div class="space-y-2">
                            <div class="option-item">
                                <input type="radio" id="c5_1" name="doctor_role" value="Rất cần thiết" class="hidden peer">
                                <label for="c5_1" class="flex items-center p-3 bg-white border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50 transition">
                                    <div class="icon-box w-4 h-4 rounded-full border border-slate-300 mr-3 flex items-center justify-center bg-white">
                                        <div class="w-2 h-2 rounded-full bg-white opacity-0 check-icon transition-opacity duration-200"></div>
                                    </div>
                                    <span class="text-sm text-slate-700">Rất cần thiết (Chẩn đoán, liều lượng, theo dõi)</span>
                                </label>
                            </div>
                             <div class="option-item">
                                <input type="radio" id="c5_2" name="doctor_role" value="Cần thiết một phần" class="hidden peer">
                                <label for="c5_2" class="flex items-center p-3 bg-white border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50 transition">
                                    <div class="icon-box w-4 h-4 rounded-full border border-slate-300 mr-3 flex items-center justify-center bg-white">
                                        <div class="w-2 h-2 rounded-full bg-white opacity-0 check-icon transition-opacity duration-200"></div>
                                    </div>
                                    <span class="text-sm text-slate-700">Cần thiết một phần (Tự tìm hiểu, nhờ bác sĩ kê đơn)</span>
                                </label>
                            </div>
                            <div class="option-item">
                                <input type="radio" id="c5_3" name="doctor_role" value="Chưa cần thiết" class="hidden peer">
                                <label for="c5_3" class="flex items-center p-3 bg-white border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50 transition">
                                    <div class="icon-box w-4 h-4 rounded-full border border-slate-300 mr-3 flex items-center justify-center bg-white">
                                        <div class="w-2 h-2 rounded-full bg-white opacity-0 check-icon transition-opacity duration-200"></div>
                                    </div>
                                    <span class="text-sm text-slate-700">Chưa thấy cần thiết (Ưu tiên sản phẩm tự nhiên)</span>
                                </label>
                            </div>
                             <div class="option-item">
                                <input type="radio" id="c5_4" name="doctor_role" value="Phân vân" class="hidden peer">
                                <label for="c5_4" class="flex items-center p-3 bg-white border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50 transition">
                                    <div class="icon-box w-4 h-4 rounded-full border border-slate-300 mr-3 flex items-center justify-center bg-white">
                                        <div class="w-2 h-2 rounded-full bg-white opacity-0 check-icon transition-opacity duration-200"></div>
                                    </div>
                                    <span class="text-sm text-slate-700">Phân vân (Không biết tìm chuyên gia ở đâu)</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between mt-8">
                        <button type="button" onclick="prevStep(1)" class="text-slate-500 hover:text-purple-700 font-medium px-4 py-2 transition">
                            <i class="fa-solid fa-chevron-left mr-2"></i> Quay lại
                        </button>
                        <button type="button" onclick="nextStep(3)" class="bg-purple-600 text-white px-6 py-2.5 rounded-lg hover:bg-purple-700 transition flex items-center shadow-lg shadow-purple-200 font-medium">
                            Tiếp tục <i class="fa-solid fa-chevron-right ml-2"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- PART 3 -->
            <div id="step3" class="step hidden fade-in">
                <div class="bg-white rounded-xl shadow-md p-6 border border-slate-100">
                    <h3 class="text-xl font-bold text-purple-800 mb-4 border-b pb-2 flex items-center">
                        <span class="bg-purple-100 text-purple-600 w-8 h-8 rounded-full flex items-center justify-center text-sm mr-2">3</span>
                        KHÓ KHĂN & MONG MUỐN
                    </h3>

                    <!-- Câu 6 -->
                    <div class="mb-4">
                        <label class="block text-slate-800 font-semibold mb-3">6. Khó khăn lớn nhất của bạn hiện nay khi tiếp cận thông tin?</label>
                        <select id="c6" name="challenges" class="block w-full p-3 border border-slate-300 rounded-lg bg-white text-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                             <option value="" disabled selected>-- Chọn khó khăn lớn nhất --</option>
                             <option value="Thông tin nhiễu loạn">Thông tin trên mạng quá nhiễu loạn, không biết đúng/sai</option>
                             <option value="Không biết nơi khám">Không biết thăm khám, xét nghiệm chẩn đoán ở đâu uy tín</option>
                             <option value="Lo sợ tác dụng phụ">Lo sợ tác dụng phụ của thuốc, không biết cách xử lý</option>
                             <option value="Chi phí">Chi phí xét nghiệm và điều trị</option>
                        </select>
                    </div>

                    <!-- Câu 7 & 8 grouped nicely -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-slate-800 font-semibold mb-3">7. Mục tiêu can thiệp ưu tiên nhất?</label>
                            <select id="c7" name="priority_goal" class="block w-full p-3 border border-slate-300 rounded-lg bg-white text-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                                <option value="Ngôn ngữ">Cải thiện ngôn ngữ và giao tiếp</option>
                                <option value="Động kinh">Giảm các cơn co giật/động kinh</option>
                                <option value="Hành vi">Cải thiện hành vi, giảm cáu kỉnh, ngủ ngon</option>
                                <option value="Vận động">Cải thiện vận động và trương lực cơ</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-slate-800 font-semibold mb-3">8. Sẵn sàng Kiêng Sữa động vật?</label>
                            <select id="c8" name="dairy_free" class="block w-full p-3 border border-slate-300 rounded-lg bg-white text-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                                <option value="Sẵn sàng">Hoàn toàn sẵn sàng</option>
                                <option value="E ngại">Sẵn sàng dùng thuốc nhưng e ngại cắt sữa</option>
                                <option value="Cần tư vấn">Cần tư vấn thêm dinh dưỡng thay thế</option>
                            </select>
                        </div>
                    </div>

                    <!-- Câu 9 -->
                    <div class="mb-6">
                        <label for="c9" class="block text-slate-800 font-semibold mb-2">
                            9. Câu hỏi cụ thể nào bạn muốn gửi đến Bác sĩ?
                        </label>
                        <textarea id="c9" name="user_question" rows="3" class="w-full p-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm" placeholder="Nhập câu hỏi của bạn tại đây..."></textarea>
                    </div>

                    <!-- Câu 10: Thông tin liên hệ -->
                    <div class="bg-purple-50 rounded-lg p-5 border border-purple-100 mb-6">
                        <label class="block text-purple-900 font-bold mb-3">
                            10. Bạn có muốn nhận thông tin về các nhà chuyên môn, xét nghiệm uy tín và tiến bộ khoa học mới nhất về CFD không?
                        </label>
                        <div class="flex items-center space-x-6 mb-4">
                            <div class="flex items-center">
                                <input type="radio" id="c10_yes" name="receive_info" value="Có" class="mr-2 accent-purple-600 w-5 h-5" onchange="toggleContactInfo(true)">
                                <label for="c10_yes" class="text-slate-700 font-medium cursor-pointer">Có, hãy gửi cho tôi</label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" id="c10_no" name="receive_info" value="Không" class="mr-2 accent-purple-600 w-5 h-5" onchange="toggleContactInfo(false)">
                                <label for="c10_no" class="text-slate-700 font-medium cursor-pointer">Không, cảm ơn</label>
                            </div>
                        </div>

                        <!-- Contact Inputs (Hidden by default) -->
                        <div id="contactFields" class="hidden space-y-3 border-t border-purple-200 pt-3 mt-3 animate-fade-in">
                            <div>
                                <input type="text" id="parentName" name="parent_name" placeholder="Tên cha/mẹ" class="w-full p-2.5 border border-slate-300 rounded focus:border-purple-500 outline-none focus:ring-1 focus:ring-purple-500">
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <input type="text" id="parentZalo" name="parent_zalo" placeholder="Số Zalo/SĐT" class="w-full p-2.5 border border-slate-300 rounded focus:border-purple-500 outline-none focus:ring-1 focus:ring-purple-500">
                                <input type="email" id="parentEmail" name="parent_email" placeholder="Email (nếu có)" class="w-full p-2.5 border border-slate-300 rounded focus:border-purple-500 outline-none focus:ring-1 focus:ring-purple-500">
                            </div>
                        </div>
                    </div>

                    <p id="surveyError" class="hidden mt-4 text-sm text-red-600 font-semibold"></p>

                    <div class="flex justify-between mt-8">
                        <button type="button" onclick="prevStep(2)" class="text-slate-500 hover:text-purple-700 font-medium px-4 py-2 transition">
                            <i class="fa-solid fa-chevron-left mr-2"></i> Quay lại
                        </button>
                        <button type="submit" class="bg-purple-700 text-white px-8 py-3 rounded-lg hover:bg-purple-800 transition shadow-lg shadow-purple-200 font-bold flex items-center">
                            Gửi Khảo Sát <i class="fa-solid fa-paper-plane ml-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <!-- Thank You Screen (Hidden) -->
        <div id="thankYouScreen" class="hidden text-center py-12 bg-white rounded-xl shadow-lg border-t-4 border-purple-500 fade-in">
            <div class="mb-6 text-purple-600 text-7xl animate-bounce">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <h2 class="text-3xl font-bold text-slate-800 mb-3">Cảm ơn Cha/Mẹ đã chia sẻ!</h2>
            <p class="text-slate-600 mb-8 px-8 max-w-lg mx-auto leading-relaxed">Thông tin của bạn đã được ghi nhận. Chúng tôi sẽ tổng hợp và sớm có những bài viết giải đáp thắc mắc dựa trên trăn trở thực tế của bạn.</p>
            <button onclick="location.reload()" class="bg-slate-100 hover:bg-slate-200 text-purple-700 font-bold py-2 px-6 rounded-full transition">
                Làm lại khảo sát
            </button>
        </div>

    </main>

    <footer class="bg-white border-t border-slate-200 py-8">
        <div class="max-w-3xl mx-auto px-4 text-center">
            <div class="text-purple-600 text-2xl mb-2"><i class="fa-solid fa-heart-pulse"></i></div>
            <p class="text-slate-500 text-sm font-medium">© 2024 Khảo sát Cộng đồng Hỗ trợ Trẻ Tự Kỷ.</p>
            <p class="text-slate-400 text-xs mt-2 max-w-lg mx-auto">Thông tin chỉ mang tính chất tham khảo và hỗ trợ chuyên môn, không thay thế chẩn đoán y khoa trực tiếp.</p>
        </div>
    </footer>

</div>
</section>

<script>
    const hhmSurvey = <?php echo wp_json_encode($survey_ajax_config, JSON_UNESCAPED_SLASHES); ?>;
    window.hhmSurvey = hhmSurvey;

    let currentStep = 1;
    const totalSteps = 3;

    function startSurvey() {
        document.getElementById('introSection').classList.add('hidden');
        document.getElementById('surveyForm').classList.remove('hidden');
        document.getElementById('surveyForm').classList.add('fade-in');
        updateProgress();
    }

    function toggleContactInfo(show) {
        const fields = document.getElementById('contactFields');
        if (show) {
            fields.classList.remove('hidden');
            document.getElementById('parentName').setAttribute('required', 'true');
            document.getElementById('parentZalo').setAttribute('required', 'true');
        } else {
            fields.classList.add('hidden');
            document.getElementById('parentName').removeAttribute('required');
            document.getElementById('parentZalo').removeAttribute('required');
        }
    }

    function nextStep(step) {
        window.scrollTo({ top: 0, behavior: 'smooth' });
        document.querySelectorAll('.step').forEach(el => el.classList.add('hidden'));
        setTimeout(() => {
            document.getElementById(`step${step}`).classList.remove('hidden');
        }, 100);
        currentStep = step;
        updateProgress();
    }

    function prevStep(step) {
        window.scrollTo({ top: 0, behavior: 'smooth' });
        document.querySelectorAll('.step').forEach(el => el.classList.add('hidden'));
        setTimeout(() => {
            document.getElementById(`step${step}`).classList.remove('hidden');
        }, 100);
        currentStep = step;
        updateProgress();
    }

    function updateProgress() {
        const percent = Math.round((currentStep / totalSteps) * 100);
        const progressBar = document.getElementById('progressBar');
        progressBar.style.width = `${percent}%`;
        document.getElementById('progressPercent').innerText = `${percent}%`;
        document.getElementById('stepLabel').innerText = `Phần ${currentStep}/${totalSteps}`;
    }

    function submitSurvey() {
        const formData = {
            symptoms: Array.from(document.querySelectorAll('input[name="symptoms"]:checked')).map(cb => cb.value),
            diagnosis_history: document.getElementById('c2').value,
            current_treatment: document.querySelector('input[name="current_treatment"]:checked')?.value || 'Not Answered',
            knowledge_gap: document.getElementById('c4').value,
            doctor_role: document.querySelector('input[name="doctor_role"]:checked')?.value || 'Not Answered',
            challenges: document.getElementById('c6').value,
            priority_goal: document.getElementById('c7').value,
            dairy_free: document.getElementById('c8').value,
            question: document.getElementById('c9').value,
            receive_info: document.querySelector('input[name="receive_info"]:checked')?.value || 'No',
            contact: {
                name: document.getElementById('parentName').value,
                zalo: document.getElementById('parentZalo').value,
                email: document.getElementById('parentEmail').value
            },
            submittedAt: new Date().toISOString()
        };

        const btn = document.querySelector('button[type="submit"]');
        const originalLabel = btn.innerHTML;
        const errorBox = document.getElementById('surveyError');
        if (errorBox) {
            errorBox.classList.add('hidden');
            errorBox.textContent = '';
        }

        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang gửi...';
        btn.disabled = true;
        btn.classList.add('opacity-75', 'cursor-not-allowed');

        const ajaxConfig = window.hhmSurvey || {};
        const ajaxUrl = ajaxConfig.ajaxUrl || '';
        const nonce = ajaxConfig.nonce || '';

        if (!ajaxUrl) {
            console.error('Thiếu AJAX URL.');
            if (errorBox) {
                errorBox.textContent = 'Không thể gửi khảo sát vì thiếu cấu hình.';
                errorBox.classList.remove('hidden');
            }
            btn.innerHTML = originalLabel;
            btn.disabled = false;
            btn.classList.remove('opacity-75', 'cursor-not-allowed');
            return;
        }

        const body = new URLSearchParams({
            action: 'hoaihel_submit_survey',
            nonce,
            payload: JSON.stringify(formData)
        });

        fetch(ajaxUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
            body
        })
            .then(res => res.json())
            .then(response => {
                if (!response.success) {
                    throw new Error(response.data?.message || 'Có lỗi xảy ra, vui lòng thử lại.');
                }
                document.getElementById('surveyForm').classList.add('hidden');
                document.getElementById('thankYouScreen').classList.remove('hidden');
                window.scrollTo({ top: 0, behavior: 'smooth' });
            })
            .catch(err => {
                if (errorBox) {
                    errorBox.textContent = err.message || 'Không thể gửi khảo sát, vui lòng thử lại.';
                    errorBox.classList.remove('hidden');
                } else {
                    alert(err.message || 'Không thể gửi khảo sát, vui lòng thử lại.');
                }
            })
            .finally(() => {
                btn.innerHTML = originalLabel;
                btn.disabled = false;
                btn.classList.remove('opacity-75', 'cursor-not-allowed');
            });
    }
</script>

<?php
get_footer();