How Tags Work
-------------

Each user can be flaged by a set of tags, seperated by slashes. For example:
"Worker/IPTLead".  For this system, here are the meanings of the tags:

 * Admin     -- Can do everything, see everything.
 * IPTLead   -- Student leads, can do most things, but cannot see admin area.
 * Editor    -- Everything a lead can do, but can be applied to non-student.  Additionally, can merge WOs.
 * Captain   -- Same as IPTLead with additional ability to give captain approval.
 * HeadCoach -- Doesn't give privilege, just marks this person as head coach.
 * Worker    -- Doesn't give privilege, just flags these as avaliable for assignment to WOs.
 * Mentor    -- Doesn't give privilage, just flags these people as mentors.
 * Guest     -- Removes all ability to update the database.  Can be used with any other tag. 

Other notes: More powerful mentors should be given the editor tag, for without it
they do not get the inbox.  Also, Editors cannot issue captain approval.  Only Admin and Captains can do that.

