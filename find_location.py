import requests

def get_location():
    try:
        response = requests.get("https://ipinfo.io/json")
        if response.status_code == 200:
            data = response.json()
            print(f"IP Address: {data.get('ip')}")
            print(f"City: {data.get('city')}")
            print(f"Region: {data.get('region')}")
            print(f"Country: {data.get('country')}")
            print(f"Location (lat,long): {data.get('loc')}")
            print(f"Organization: {data.get('org')}")
        else:
            print("Could not retrieve location information.")
    except Exception as e:
        print(f"Error: {e}")

def get_location_by_phone(phone_number, api_key):
    try:
        url = f"http://apilayer.net/api/validate?access_key={api_key}&number={phone_number}"
        response = requests.get(url)
        if response.status_code == 200:
            data = response.json()
            if data.get("valid"):
                print(f"Phone Number: {data.get('international_format')}")
                print(f"Country: {data.get('country_name')}")
                print(f"Location: {data.get('location')}")
                print(f"Carrier: {data.get('carrier')}")
                print(f"Line type: {data.get('line_type')}")
            else:
                print("Invalid phone number or no data found.")
        else:
            print("Could not retrieve phone location information.")
    except Exception as e:
        print(f"Error: {e}")

if __name__ == "__main__":
    print("1. Find location by IP")
    print("2. Find location by phone number")
    choice = input("Choose an option (1 or 2): ")
    if choice == "1":
        get_location()
    elif choice == "2":
        phone = input("Enter phone number with country code (e.g., +14158586273): ")
        api_key = input("Enter your NumVerify API key: ")
        get_location_by_phone(phone, api_key)
    else:
        print("Invalid choice.")