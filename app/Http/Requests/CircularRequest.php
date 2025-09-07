<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CircularRequest extends FormRequest
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
        $rules = [
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'archivo' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // 10MB máximo
            'fecha_expiracion' => 'nullable|date|after:today',
            'es_global' => 'nullable|boolean'
        ];

        // Si no es global, grupo_id y seccion son requeridos
        if (!$this->input('es_global')) {
            $rules['grupo_id'] = 'required|exists:grupos,id';
            $rules['seccion'] = 'required|in:Primaria,Secundaria';
        } else {
            $rules['grupo_id'] = 'nullable|exists:grupos,id';
            $rules['seccion'] = 'nullable|in:Primaria,Secundaria';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'titulo.required' => 'El título de la circular es obligatorio.',
            'titulo.max' => 'El título no puede tener más de 255 caracteres.',
            'descripcion.max' => 'La descripción no puede tener más de 1000 caracteres.',
            'archivo.required' => 'Debe seleccionar un archivo para la circular.',
            'archivo.file' => 'El archivo seleccionado no es válido.',
            'archivo.mimes' => 'El archivo debe ser de tipo: PDF, DOC, DOCX, JPG, JPEG o PNG.',
            'archivo.max' => 'El archivo no puede ser mayor a 10MB.',
            'grupo_id.required' => 'Debe seleccionar un grupo para circulares no globales.',
            'grupo_id.exists' => 'El grupo seleccionado no existe.',
            'seccion.required' => 'Debe seleccionar una sección para circulares no globales.',
            'seccion.in' => 'La sección debe ser Primaria o Secundaria.',
            'fecha_expiracion.date' => 'La fecha de expiración debe ser una fecha válida.',
            'fecha_expiracion.after' => 'La fecha de expiración debe ser posterior a hoy.'
        ];
    }
}
