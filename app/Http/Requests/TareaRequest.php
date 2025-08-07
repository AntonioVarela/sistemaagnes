<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TareaRequest extends FormRequest
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
            'descripcion' => 'required|string|max:5000',
            'fecha_entrega' => 'required|date|after_or_equal:today',
            'hora_entrega' => 'nullable|date_format:H:i',
            'archivo' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,jpg,jpeg,png,gif,zip,rar|max:10240', // 10MB máximo
            'grupo' => 'nullable|exists:grupos,id',
            'materia' => 'nullable|exists:materias,id',
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
            'descripcion.required' => 'La descripción es obligatoria.',
            'descripcion.max' => 'La descripción no puede tener más de 5000 caracteres.',
            'fecha_entrega.required' => 'La fecha de entrega es obligatoria.',
            'fecha_entrega.date' => 'La fecha de entrega debe ser una fecha válida.',
            'fecha_entrega.after_or_equal' => 'La fecha de entrega debe ser hoy o una fecha futura.',
            'hora_entrega.date_format' => 'La hora de entrega debe tener el formato HH:MM.',
            'archivo.file' => 'El archivo debe ser un archivo válido.',
            'archivo.mimes' => 'El archivo debe ser de tipo: pdf, doc, docx, xls, xlsx, ppt, pptx, txt, jpg, jpeg, png, gif, zip, rar.',
            'archivo.max' => 'El archivo no puede ser mayor a 10MB.',
            'grupo.exists' => 'El grupo seleccionado no existe.',
            'materia.exists' => 'La materia seleccionada no existe.',
        ];
    }
}
