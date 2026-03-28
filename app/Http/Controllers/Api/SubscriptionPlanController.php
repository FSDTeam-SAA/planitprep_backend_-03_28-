<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubscriptionPlan;

class SubscriptionPlanController extends Controller
{
    // Get all plans
    public function index()
    {
        return response()->json(
            SubscriptionPlan::all()
        );
    }

    // Create new plan
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'price' => 'required|string',
            'duration' => 'required|string',
            'price_id' => 'required|string',
            'features' => 'required|array',
            'highlight' => 'boolean',
        ]);

        // Check if plan with same price_id already exists
        $existingPlan = SubscriptionPlan::where('price_id', $request->price_id)->first();

        if ($existingPlan) {
            return response()->json([
                'message' => 'Plan already exists with this price_id'
            ], 400);
        }

        $plan = SubscriptionPlan::create([
            'title' => $request->title,
            'price' => $request->price,
            'duration' => $request->duration,
            'price_id' => $request->price_id,
            'highlight' => $request->highlight ?? false,
            'features' => $request->features,
        ]);

        return response()->json([
            'message' => 'Plan created successfully',
            'data' => $plan
        ]);
    }

    // Update plan using price_id from request body
    public function update(Request $request)
    {
        $request->validate([
            'price_id' => 'required|string',
            'title' => 'sometimes|string',
            'price' => 'sometimes|string',
            'duration' => 'sometimes|string',
            'features' => 'sometimes|array',
            'highlight' => 'sometimes|boolean',
        ]);
    
        $plan = SubscriptionPlan::where('price_id', $request->price_id)->first();
    
        if (!$plan) {
            return response()->json([
                'message' => 'Plan not found'
            ], 404);
        }
    
        $plan->update($request->only([
            'title',
            'price',
            'duration',
            'features',
            'highlight',
        ]));
    
        return response()->json([
            'message' => 'Plan updated successfully',
            'data' => $plan
        ]);
    }


    // Delete plan using price_id from request body
    public function destroy(Request $request)
    {
        $request->validate([
            'price_id' => 'required|string',
        ]);

        $plan = SubscriptionPlan::where('price_id', $request->price_id)->first();

        if (!$plan) {
            return response()->json([
                'message' => 'Plan not found'
            ], 404);
        }

        $plan->delete();

        return response()->json([
            'message' => 'Plan deleted successfully'
        ]);
    }

    // Get all subscription plans
    public function getPlans()
    {
        $plans = SubscriptionPlan::orderBy('highlight', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->get();
    
        // If no plans found
        if ($plans->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No subscription plans available',
                'data' => []
            ], 200);
        }
    
        // If plans exist
        return response()->json([
            'status' => true,
            'message' => 'Subscription plans fetched successfully',
            'data' => $plans
        ], 200);
    }



}
