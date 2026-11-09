<?php

use App\Http\Controllers\AlertaController;
use App\Http\Controllers\DispositivoController;
use App\Http\Controllers\SensorController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
//? Dispositivos
Route::get('/dispositivos',[DispositivoController::class,'index'])->name('dispositivos.index');
Route::get('/dispositivos/{id}',[DispositivoController::class,'show'])->whereNumber('id')->name('dispositivos.show');
Route::get('/dispositivos/create',[DispositivoController::class,'create'])->name('dispositivos.create');
Route::post('/dispositivos/store',[DispositivoController::class,'store'])->name('dispositivos.store');
Route::get('/dispositivos/{id}/edit',[DispositivoController::class,'edit'])->whereNumber('id')->name('dispositivos.edit');
Route::put('/dispositivos/{id}',[DispositivoController::class,'update'])->whereNumber('id')->name('dispositivos.update');
Route::delete('/dispositivos/{id}/destroy',[DispositivoController::class,'destroy'])->whereNumber('id')->name('dispositivos.delete');
//? Sensores
Route::get('/sensores/{id}',[SensorController::class,'show'])->whereNumber('id')->name('sensores.show');
Route::get('/sensores/edit/{id}',[SensorController::class,'edit'])->whereNumber('id')->name('sensores.edit');
Route::get('/dispositivos/{id}/sensores/create',[SensorController::class,'create'])->whereNumber('id')->name('sensores.create');
Route::post('/dispositivos/{id}/sensores',[SensorController::class,'store'])->name('sensores.store');
Route::put('/sensores/{id}',[SensorController::class,'update'])->whereNumber('id')->name('sensores.update');
Route::delete('/sensores/{id}',[SensorController::class,'destroy'])->name('sensores.destroy');
//? Alerta
Route::get('/alertas',[AlertaController::class,'index'])->name('alertas.index');
//Route::get('/alertas/{id}',[AlertaController::class,'show'])->whereNumber('id')->name('alertas.show');
Route::patch('/sensores/{id}/simulacion-puerta', [SensorController::class, 'simularPuerta'])
    ->whereNumber('id')
    ->name('sensores.simular-puerta');
