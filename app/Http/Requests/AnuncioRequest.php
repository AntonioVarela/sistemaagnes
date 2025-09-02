<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnuncioRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'titulo' => 'required|string|max:255',
            'contenido' => 'required|string|max:5000',
            'archivo' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,jpg,jpeg,png,gif,zip,rar|max:10240', // 10MB máximo
            'fecha_expiracion' => 'nullable|date|after_or_equal:today',
            'grupo_id' => 'nullable|exists:grupos,id',
            'materia_id' => 'nullable|exists:materias,id',
            'es_global' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'titulo.required' => 'El título es obligatorio.',
            'titulo.max' => 'El título no puede tener más de 255 caracteres.',
            'contenido.required' => 'El contenido es obligatorio.',
            'contenido.max' => 'El contenido no puede tener más de 5000 caracteres.',
            'archivo.file' => 'El archivo debe ser un archivo válido.',
            'archivo.mimes' => 'El archivo debe ser de tipo: pdf, doc, docx, xls, xlsx, ppt, pptx, txt, jpg, jpeg, png, gif, zip, rar.',
            'archivo.max' => 'El archivo no puede ser mayor a 10MB.',
            'fecha_expiracion.date' => 'La fecha de expiración debe ser una fecha válida.',
            'fecha_expiracion.after_or_equal' => 'La fecha de expiración debe ser hoy o una fecha futura.',
            'grupo_id.exists' => 'El grupo seleccionado no existe.',
            'materia_id.exists' => 'La materia seleccionada no existe.',
        ];
    }
}
