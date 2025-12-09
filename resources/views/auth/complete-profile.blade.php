<x-opac.layout title="Lengkapi Profil">
    <div class="min-h-[70vh] flex items-center justify-center py-8 px-4">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 overflow-hidden">
                <div class="bg-gradient-to-r from-primary-600 to-primary-800 p-6 text-center">
                    <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-user-edit text-3xl text-white"></i>
                    </div>
                    <h1 class="text-xl font-bold text-white">Lengkapi Profil</h1>
                    <p class="text-primary-200 text-sm mt-1">Lengkapi data untuk melanjutkan</p>
                </div>

                <div class="p-6">
                    @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-600 text-sm p-4 rounded-xl mb-5">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="bg-gray-50 rounded-xl p-4 mb-5">
                        <p class="text-sm text-gray-600"><span class="font-medium">{{ $member->name }}</span></p>
                        <p class="text-xs text-gray-400">{{ $member->email }}</p>
                    </div>

                    <form action="{{ route('member.complete-profile') }}" method="POST" class="space-y-4" x-data="profileForm()">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">NIM</label>
                            <input type="text" name="nim" value="{{ old('nim') }}" required placeholder="Contoh: 2021001234"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kampus</label>
                            <select name="branch_id" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500">
                                <option value="">Pilih Kampus</option>
                                @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fakultas</label>
                            <select name="faculty_id" x-model="facultyId" @change="loadDepartments()" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500">
                                <option value="">Pilih Fakultas</option>
                                @foreach($faculties as $faculty)
                                <option value="{{ $faculty->id }}">{{ $faculty->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Program Studi</label>
                            <select name="department_id" x-model="departmentId" required :disabled="!facultyId" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 disabled:bg-gray-100">
                                <option value="">Pilih Program Studi</option>
                                <template x-for="dept in departments" :key="dept.id">
                                    <option :value="dept.id" x-text="dept.name"></option>
                                </template>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                                <select name="gender" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500">
                                    <option value="">Pilih</option>
                                    <option value="L" {{ old('gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
                                <input type="text" name="phone" value="{{ old('phone') }}" required 
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500">
                            </div>
                        </div>
                        <button type="submit" class="w-full py-3 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-semibold rounded-xl shadow-lg shadow-primary-500/30 hover:shadow-xl transition">
                            <i class="fas fa-check mr-2"></i> Simpan & Lanjutkan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    function profileForm() {
        return {
            facultyId: '{{ old('faculty_id') }}',
            departmentId: '{{ old('department_id') }}',
            departments: [],
            async loadDepartments() {
                if (!this.facultyId) {
                    this.departments = [];
                    return;
                }
                const res = await fetch(`/api/departments?faculty_id=${this.facultyId}`);
                this.departments = await res.json();
            },
            init() {
                if (this.facultyId) this.loadDepartments();
            }
        }
    }
    </script>
</x-opac.layout>
