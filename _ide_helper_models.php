<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property string $name Nama alternatif, contoh: Web Application Firewall (WAF)
 * @property string|null $description Penjelasan alternatif, cara implementasi, dan konteks penggunaannya
 * @property string $source Sumber alternatif. Admin = template dari admin, User = alternatif diinput mandiri oleh user
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Assessment> $assessments
 * @property-read int|null $assessments_count
 * @property-read \App\Models\User|null $creator
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\EdasResult> $edasResults
 * @property-read int|null $edas_results_count
 * @property-read string $source_label
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AlternativeValue> $values
 * @property-read int|null $values_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alternative createdBy(int $userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alternative fromAdmin()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alternative fromUser()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alternative newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alternative newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alternative query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alternative whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alternative whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alternative whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alternative whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alternative whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alternative whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alternative whereUpdatedAt($value)
 */
	class Alternative extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $assessment_id
 * @property int $alternative_id
 * @property int $criteria_id
 * @property float $value Nilai alternatif untuk kriteria ini. Bisa berupa skor atau nilai numerik lain sesuai jenis kriteria
 * @property int|null $input_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Alternative $alternative
 * @property-read \App\Models\Assessment $assessment
 * @property-read \App\Models\Criteria $criteria
 * @property-read \App\Models\User|null $inputBy
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlternativeValue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlternativeValue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlternativeValue query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlternativeValue whereAlternativeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlternativeValue whereAssessmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlternativeValue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlternativeValue whereCriteriaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlternativeValue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlternativeValue whereInputBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlternativeValue whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlternativeValue whereValue($value)
 */
	class AlternativeValue extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $title Judul assessment, contoh: Analisis Risiko Jaringan Q3 2026
 * @property string|null $description Konteks dan latar belakang assessment
 * @property string $status Status assessment. Draft = belum selesai, Completed = EDAS sudah dikalkulasikan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AlternativeValue> $alternativeValues
 * @property-read int|null $alternative_values_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Alternative> $alternatives
 * @property-read int|null $alternatives_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AuditLog> $auditLogs
 * @property-read int|null $audit_logs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\EdasResult> $edasResults
 * @property-read int|null $edas_results_count
 * @property-read string $status_color
 * @property-read string $status_label
 * @property-read \App\Models\User $owner
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\EdasResult> $rankedResults
 * @property-read int|null $ranked_results_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assessment completed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assessment draft()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assessment forUser(int $userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assessment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assessment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assessment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assessment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assessment whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assessment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assessment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assessment whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assessment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assessment whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assessment withFullDetail()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assessment withResults()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Assessment withSummary()
 */
	class Assessment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $assessment_id
 * @property int $alternative_id
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentAlternative newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentAlternative newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentAlternative query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentAlternative whereAlternativeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentAlternative whereAssessmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentAlternative whereId($value)
 */
	class AssessmentAlternative extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $user_id
 * @property string $action Jenis aksi: created, updated, deleted, login, logout, calculate_edas, dsb.
 * @property string $table_name Nama tabel yang terdampak, contoh: assessments, alternatives, criteria, dsb.
 * @property int|null $record_id ID record yang terdampak di tabel tersebut
 * @property array<array-key, mixed>|null $old_data Data sebelum perubahan (null untuk created)
 * @property array<array-key, mixed>|null $new_data Data setelah perubahan (null untuk deleted)
 * @property string|null $ip_address IP address client. 45 untuk IPv6 support
 * @property \Illuminate\Support\Carbon $created_at Waktu aksi dilakukan
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog forRecord(string $table, int $id)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog forTable(string $table)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereNewData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereOldData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereRecordId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereTableName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereUserId($value)
 */
	class AuditLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name Nama kriteria, contoh: Efektivitas Mitigasi
 * @property string|null $description Penjelasan kriteria dan cara penilaiannya
 * @property string $type Tipe kriteria, benefit berarti semakin tinggi nilainya semakin baik, cost berarti semakin rendah nilainya semakin baik
 * @property float $weight Bobot kriteria (0.0000 - 1.0000). Total bobot ideal adalah 1.0000
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AlternativeValue> $alternativeValues
 * @property-read int|null $alternative_values_count
 * @property-read string $weight_percent
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Criteria benefits()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Criteria costs()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Criteria newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Criteria newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Criteria query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Criteria whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Criteria whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Criteria whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Criteria whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Criteria whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Criteria whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Criteria whereWeight($value)
 */
	class Criteria extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $assessment_id
 * @property int $alternative_id
 * @property float $pda Positive Distance from Average — jarak positif terbobot dari AV
 * @property float $nda Negative Distance from Average — jarak negatif terbobot dari AV
 * @property float $sp Penjumlahan terbobot dari PDA (SP = total(w * PDA))
 * @property float $sn Penjumlahan terbobot dari NDA (SN = total(w * NDA))
 * @property float $nsp Normalized SP (NSP = SP / max(SP), range 0-1)
 * @property float $nsn Normalized SN (NSN = 1 - (SN / max(SN)), range 0-1)
 * @property float $appraisal_score Appraisal Score (AS = (NSP + NSN) / 2, range 0-1)
 * @property int $rank Peringkat alternatif (1 = terbaik), urut berdasarkan appraisal_score dari yang tertinggi ke terendah
 * @property \Illuminate\Support\Carbon $calculated_at Waktu kalkulasi EDAS dijalankan
 * @property-read \App\Models\Alternative $alternative
 * @property-read \App\Models\Assessment $assessment
 * @property-read string $as_score_formatted
 * @property-read string $quality_color
 * @property-read string $quality_label
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EdasResult newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EdasResult newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EdasResult query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EdasResult ranked()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EdasResult topRanked()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EdasResult whereAlternativeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EdasResult whereAppraisalScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EdasResult whereAssessmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EdasResult whereCalculatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EdasResult whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EdasResult whereNda($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EdasResult whereNsn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EdasResult whereNsp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EdasResult wherePda($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EdasResult whereRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EdasResult whereSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EdasResult whereSp($value)
 */
	class EdasResult extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string $role
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Assessment> $assessments
 * @property-read int|null $assessments_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

