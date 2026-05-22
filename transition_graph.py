import time

# Define states and their display times (in seconds)
states = ["Green", "Yellow", "Red"]
durations = {
    "Green": 5,
    "Yellow": 2,
    "Red": 5
}

def traffic_light_one_cycle():
    print("🚦 Traffic Light Simulation (One Full Cycle)\n")
    
    # Start from Green and go through each state once
    for state in states:
        print(f"Light is: {state}")
        time.sleep(durations[state])  # Wait for the duration of the current light
    
    # After completing Red → Green transition once
    print("\n✅ Cycle complete! Traffic light simulation finished.")

# Run the traffic light system
traffic_light_one_cycle()
