# CI-ZarinPal
Codeigniter 3.x library for ZarinPal payment gateway

## How to install
Copy `Zarinpal.php` to `application/libraries` of your own project.

## How to use
First, load library:
```
$this->load->library('zarinpal', [
    'merchant_id' => 'YOUR_MERCHANT_ID'
]);
```

For sending user to gateway:
```
if ($this->zarinpal->request($amount, $description, $callback))
{
    $authority = $this->zarinpal->get_authority();
    // do database stuff
    $this->zarinpal->redirect();
}
else
{
    // Unable to connect to gateway
}
```

For verifying user's payment:
```
$status = $this->input->get('Status', TRUE);
$authority = $this->input->get('Authority', TRUE);

if ($status !== 'OK' OR $authority === NULL)
{
    // payment canceled by user
}

if ($this->zarinpal->verify($amount, $authority))
{
    $ref_id = $this->zarinpal->get_ref_id();
    // payment succeeded, do database stuff   
}
else
{
    $error = $this->zarinpal->get_error()
    // payment failed
}
```

## Sandbox
For testing purposes, you can turn on sandbox mode:
```
$this->zarinpal->sandbox();
```
