<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseEnrollment;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class CourseEnrollmentController extends Controller
{
    public function show(string $courseSlug): Renderable
    {
        /** @var Course $course */
        $course = Course::query()->where('slug', $courseSlug)->firstOrFail();

        /** @var CourseEnrollment $enrollment */
        $enrollment = CourseEnrollment::query()
            ->with('course.lessons')
            ->where('course_id', $course->id)
            ->where('user_id', auth()->id())
            ->first();

        if ($enrollment === null) {
            return view('courses.show', ['course' => $course]);
        }


        $scoreStatistics = $course->getCourseStatistics(Auth::user());

        return view('courseEnrollments.show', ['enrollment' => $enrollment, 'scoreStatistics' => $scoreStatistics]);
    }

    public function store(string $courseSlug): RedirectResponse
    {
        /** @var Course $course */
        $course = Course::query()->where('slug', $courseSlug)->firstOrFail();

        $course->enroll(auth()->user());

        return redirect()->action([self::class, 'show'], [$course->slug]);
    }
}
