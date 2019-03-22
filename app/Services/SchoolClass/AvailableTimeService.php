<?php

namespace App\Services\SchoolClass;

use App\Models\SchoolClass;

class AvailableTimeService
{
    /**
     * Retorna se matrícula não possui enturmação em horário conflitante com a turma enviada por parâmetro
     *
     * @param int      $studentId     ID do aluno
     * @param int      $schoolClassId ID da turma
     *
     * @return bool
     */
    public function isAvailable(int $studentId, int $schoolClassId)
    {
        $schoolClass = SchoolClass::findOrFail($schoolClassId);

        if ($schoolClass->tipo_mediacao_didatico_pedagogico != 1) {
            return true;
        }

        $otherSchoolClass = SchoolClass::where('cod_turma', '<>', $schoolClassId)
            ->whereHas('enrollments', function($enrollmentsQuery) use ($studentId){
                $enrollmentsQuery->whereHas('registration', function($registrationQuery) use ($studentId) {
                    $registrationQuery->where('ref_cod_aluno', $studentId);
                })->where('ativo', 1);
            })->get();

        foreach ($otherSchoolClass as $otherSchoolClass) {
            if ($this->schedulesMatch($schoolClass, $otherSchoolClass)) {
                return false;
            }
        }

        return true;
    }

    private function schedulesMatch(SchoolClass $schoolClass, SchoolClass $otherSchoolClass)
    {
        if ($otherSchoolClass->tipo_mediacao_didatico_pedagogico != 1) {
            return false;
        }

        if (empty($schoolClass->dias_semana) || empty($otherSchoolClass->dias_semana)) {
            return false;
        }

        $weekdaysMatches = array_intersect($schoolClass->dias_semana, $otherSchoolClass->dias_semana);
        if (empty($weekdaysMatches)) {
            return false;
        }

        return $schoolClass->hora_inicial <= $otherSchoolClass->hora_final && $schoolClass->hora_final >= $otherSchoolClass->hora_inicial;
    }
}