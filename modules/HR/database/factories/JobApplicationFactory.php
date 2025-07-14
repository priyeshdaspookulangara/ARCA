<?php

namespace Modules\HR\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\HR\TalentManagement\Domain\Entities\JobApplication;
use Modules\HR\PersonnelAdmin\Domain\Entities\Job;

class JobApplicationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = JobApplication::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $job = Job::factory()->create();

        return [
            'hr_job_id' => $job->id,
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->optional()->phoneNumber,
            'resume_path' => $this->faker->optional()->filePath(),
            'cover_letter' => $this->faker->optional()->paragraph,
            'status' => $this->faker->randomElement([
                JobApplication::STATUS_APPLIED,
                JobApplication::STATUS_SCREENING,
                JobApplication::STATUS_INTERVIEWING,
                JobApplication::STATUS_REJECTED,
            ]),
            'applied_date' => $this->faker->dateTimeThisYear()->format('Y-m-d'),
            'notes' => $this->faker->optional()->sentence,
        ];
    }

    public function forJob(Job $job)
    {
        return $this->state(['hr_job_id' => $job->id]);
    }

    public function withStatus(string $status)
    {
        return $this->state(['status' => $status]);
    }
}
