# Security issue disclosure procedure

If you think that you have found a security issue in flysystem-bundle, don't use the bug tracker and
don't publish it publicly. Instead, all security issues must be sent to galopintitouan [at] gmail.com.

For each report, the core maintainers of flysystem-bundle will first try to confirm the vulnerability.
When it is confirmed, we will work on a solution following these steps:

1. Send an acknowledgement to the reporter;
2. Work on a patch in a dedicated private repository;
3. Get a CVE identifier from [mitre.org](https://cveform.mitre.org/);
4. Send the patch to the reporter for review;
5. Apply the patch to all maintained versions of flysystem-bundle;
6. Package new versions for all affected versions;
7. Update the [public security advisories database](https://github.com/FriendsOfPHP/security-advisories)
   maintained by the FriendsOfPHP organization.
