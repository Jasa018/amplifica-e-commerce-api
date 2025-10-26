@extends('layouts.app')

@section('title', 'Ver Usuario')

@section('content')
<div class="bg-white overflow-hidden shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Detalles del Usuario</h2>
            <div class="space-x-2">
                <a href="{{ route('users.edit', $user) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                    Editar
                </a>
                <a href="{{ route('users.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Volver
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">ID</label>
                <p class="mt-1 text-sm text-gray-900">{{ $user->id }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Nombre</label>
                <p class="mt-1 text-sm text-gray-900">{{ $user->name }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <p class="mt-1 text-sm text-gray-900">{{ $user->email }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Fecha de Creación</label>
                <p class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('d/m/Y H:i') }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Última Actualización</label>
                <p class="mt-1 text-sm text-gray-900">{{ $user->updated_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection