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

                    <div class="bg-gray-50 rounded-xl p-4 mb-5 flex items-center gap-3">
                        <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm">
                            <svg class="w-5 h-5" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-700">{{ $member->name }}</p>
                            <p class="text-xs text-gray-400">{{ $member->email }}</p>
                        </div>
                    </div>

                    <form action="{{ route('member.complete-profile') }}" method="POST" class="space-y-4" x-data="profileForm()">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">NIM</label>
                            <input type="text" name="nim" value="{{ old('nim') }}" required placeholder="Contoh: 402019611021"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kampus</label>
                                <select name="branch_id" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500">
                                    <option value="">Pilih</option>
                                    @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Anggota</label>
                                <select name="member_type_id" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500">
                                    <option value="">Pilih</option>
                                    @foreach($memberTypes as $type)
                                    <option value="{{ $type->id }}" {{ old('member_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
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
                                    <option value="M" {{ old('gender') == 'M' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="F" {{ old('gender') == 'F' ? 'selected' : '' }}>Perempuan</option>
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
