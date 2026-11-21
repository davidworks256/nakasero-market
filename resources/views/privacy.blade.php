<x-app-layout title="Privacy notice">
    <x-slot name="header">Privacy notice</x-slot>
    <x-slot name="subheader">How the Nakasero Seller–Buyer Digital Marketplace collects, uses and protects your data.</x-slot>

    <div class="max-w-4xl mx-auto">
        {{-- At a glance --}}
        <div class="bg-white border border-gray-200 rounded-lg p-5">
            <p class="text-sm font-semibold text-gray-900">At a glance</p>
            <ul class="mt-3 grid sm:grid-cols-3 gap-3 text-sm text-gray-700">
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 shrink-0 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    We collect the minimum needed to connect a buyer and a seller.
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 shrink-0 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Your phone and email are only shared after you choose to make contact.
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 shrink-0 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    You can correct or permanently delete your data yourself, any time.
                </li>
            </ul>
            <p class="mt-3 text-xs text-gray-500">Last updated: November 2026 · Applies to this demo deployment.</p>
        </div>

        {{-- Contents --}}
        <nav class="mt-6 bg-white border border-gray-200 rounded-lg p-5" aria-label="Contents">
            <p class="text-sm font-semibold text-gray-900">Contents</p>
            <ol class="mt-3 grid sm:grid-cols-2 gap-x-6 gap-y-2 text-sm list-decimal list-inside">
                <li><a href="#collect" class="text-gray-700 underline-offset-2 hover:underline">What we collect</a></li>
                <li><a href="#why" class="text-gray-700 underline-offset-2 hover:underline">Why we need it</a></li>
                <li><a href="#contact-details" class="text-gray-700 underline-offset-2 hover:underline">Who sees your contact details</a></li>
                <li><a href="#storage" class="text-gray-700 underline-offset-2 hover:underline">Where your data lives</a></li>
                <li><a href="#security" class="text-gray-700 underline-offset-2 hover:underline">How we protect it</a></li>
                <li><a href="#rights" class="text-gray-700 underline-offset-2 hover:underline">Your rights</a></li>
                <li><a href="#deletion" class="text-gray-700 underline-offset-2 hover:underline">Deleting your account</a></li>
                <li><a href="#contact" class="text-gray-700 underline-offset-2 hover:underline">Contact us</a></li>
            </ol>
        </nav>

        {{-- Sections --}}
        <div class="mt-6 space-y-6">
            <section id="collect" class="bg-white border border-gray-200 rounded-lg p-6 scroll-mt-24">
                <h2 class="text-base font-bold text-gray-900">1. What we collect</h2>
                <ul class="mt-3 space-y-3 text-sm text-gray-700">
                    <li><span class="font-semibold">Account data</span> — your name, email address, phone number and a hashed password. Passwords are stored as bcrypt hashes and are never kept in plain text.</li>
                    <li><span class="font-semibold">Seller data</span> — your market stall and trading location, plus the produce listings (title, description, unit, price, quantity, photos) you publish.</li>
                    <li><span class="font-semibold">Communication data</span> — messages exchanged in the inbox and the order requests between buyers and sellers.</li>
                    <li><span class="font-semibold">Price history</span> — when a listing price changes, the new price is recorded to support future market-price insights.</li>
                    <li><span class="font-semibold">Consent record</span> — the date and time you accepted this notice during registration.</li>
                </ul>
            </section>

            <section id="why" class="bg-white border border-gray-200 rounded-lg p-6 scroll-mt-24">
                <h2 class="text-base font-bold text-gray-900">2. Why we need it</h2>
                <p class="mt-3 text-sm text-gray-700">Every field exists to make one deal happen: a buyer finding the right produce at a fair price from a verified seller. We use your data to show listings, unlock contact between parties who choose to talk, keep a record of order requests, and let the market association verify sellers.</p>
                <p class="mt-3 text-sm text-gray-700"><span class="font-semibold">What we never do:</span> sell your data, share it with advertisers, or use it for anything unrelated to trading on the marketplace.</p>
            </section>

            <section id="contact-details" class="bg-white border border-gray-200 rounded-lg p-6 scroll-mt-24">
                <h2 class="text-base font-bold text-gray-900">3. Who sees your contact details</h2>
                <ol class="mt-3 space-y-3 text-sm text-gray-700 list-decimal list-inside">
                    <li><span class="font-semibold">Browsing:</span> anyone — including logged-out visitors — sees only your produce, prices, stall and location. No phone, no email.</li>
                    <li><span class="font-semibold">After you message or order:</span> your name and phone are revealed to that one counterpart, so you can agree pickup and payment.</li>
                    <li><span class="font-semibold">Market admin:</span> the association team can view accounts for verification and abuse handling only.</li>
                </ol>
            </section>

            <section id="storage" class="bg-white border border-gray-200 rounded-lg p-6 scroll-mt-24">
                <h2 class="text-base font-bold text-gray-900">4. Where your data lives</h2>
                <p class="mt-3 text-sm text-gray-700">Data is stored in the marketplace's own database and file storage (for listing photos), backed up daily. If you delete your account, your messages and order history are anonymised rather than erased, so price records stay accurate for the farmers who keep trading — the rest of your personal data is removed.</p>
            </section>

            <section id="security" class="bg-white border border-gray-200 rounded-lg p-6 scroll-mt-24">
                <h2 class="text-base font-bold text-gray-900">5. How we protect it</h2>
                <ul class="mt-3 grid sm:grid-cols-2 gap-2 text-sm text-gray-700">
                    <li class="flex items-start gap-2"><span class="text-black font-bold">•</span> HTTPS in production and strict security response headers</li>
                    <li class="flex items-start gap-2"><span class="text-black font-bold">•</span> bcrypt password hashing</li>
                    <li class="flex items-start gap-2"><span class="text-black font-bold">•</span> Rate limits on login, registration, messaging and orders</li>
                    <li class="flex items-start gap-2"><span class="text-black font-bold">•</span> Role and ownership checks enforced on the server, not just the browser</li>
                </ul>
            </section>

            <section id="rights" class="bg-white border border-gray-200 rounded-lg p-6 scroll-mt-24">
                <h2 class="text-base font-bold text-gray-900">6. Your rights</h2>
                <p class="mt-3 text-sm text-gray-700">Under Uganda's Data Protection and Privacy Act (2019) you have the right to access your personal data, correct anything inaccurate, object to its processing, and ask for erasure. Access and correction are self-service on your <a href="{{ route('profile.edit') }}" class="underline hover:text-gray-900">profile page</a>; for anything else, use the contact details below.</p>
            </section>

            <section id="deletion" class="bg-white border border-gray-200 rounded-lg p-6 scroll-mt-24">
                <h2 class="text-base font-bold text-gray-900">7. Deleting your account</h2>
                <p class="mt-3 text-sm text-gray-700">You can permanently delete your account from the danger zone of your <a href="{{ route('profile.edit') }}" class="underline hover:text-gray-900">profile page</a>. You will be asked to confirm your password. Deleting removes your listings, profile and personal details; unanswered conversations and records needed for ongoing trades are anonymised.</p>
            </section>

            <section id="contact" class="bg-white border border-gray-200 rounded-lg p-6 scroll-mt-24">
                <h2 class="text-base font-bold text-gray-900">8. Contact us</h2>
                <address class="mt-3 not-italic text-sm text-gray-700 space-y-1">
                    <p>The Data Officer, Nakasero Market Association</p>
                    <p>Nakasero Market, Kabyoke–Kisenyi Road, Kampala</p>
                    <p>Phone: +256 414 234 567 <span class="text-gray-400">(demo)</span></p>
                    <p>Email: privacy@nakasero.test <span class="text-gray-400">(demo)</span></p>
                </address>
            </section>
        </div>

        <div class="mt-8">
            <a href="{{ url()->previous() }}" class="inline-block px-5 py-2.5 bg-black text-white text-sm font-semibold rounded-lg hover:bg-gray-800">← Back</a>
        </div>
    </div>
</x-app-layout>
